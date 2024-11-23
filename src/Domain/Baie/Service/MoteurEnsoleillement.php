<?php

namespace App\Domain\Baie\Service;

use App\Domain\Baie\Baie;
use App\Domain\Baie\Data\{C1Collection, C1Repository, Fe1Repository, Fe2Repository, OmbRepository, SwRepository};
use App\Domain\Baie\Entity\{MasqueLointain, MasqueProche};
use App\Domain\Baie\Enum\{NatureMenuiserie, SecteurChampsVision, TypeBaie, TypeMasqueLointain, TypeMasqueProche, TypePose, TypeVitrage};
use App\Domain\Baie\ValueObject\{Ensoleillement, EnsoleillementCollection};
use App\Domain\Common\Enum\{Mois, Orientation, ZoneClimatique};

final class MoteurEnsoleillement
{
    public function __construct(
        private C1Repository $c1_repository,
        private SwRepository $sw_repository,
        private Fe1Repository $fe1_repository,
        private Fe2Repository $fe2_repository,
        private OmbRepository $omb_repository,
    ) {}

    public function calcule_ensoleillement(Baie $entity): EnsoleillementCollection
    {
        $collection = [];

        $fe1 = $this->calcule_fe1($entity);
        $fe2 = $this->calcule_fe2($entity);
        $omb = $this->calcule_omb($entity);
        $fe = $this->fe(fe1: $fe1, fe2: $fe2, omb: $omb);

        $sw = $this->calcule_sw($entity);

        $c1_collection = $this->c1_collection(
            zone_climatique: $entity->enveloppe()->audit()->zone_climatique(),
            inclinaison: $entity->caracteristique()->inclinaison,
            orientation: $entity->orientation() ? Orientation::from_azimut($entity->orientation()) : null
        );

        foreach (Mois::cases() as $mois) {
            $c1 = $c1_collection->find($mois)->c1;
            $sse = $this->sse(surface: $entity->surface_deperditive(), sw: $sw, fe: $fe, c1: $c1);

            if ($entity->local_non_chauffe()?->est_ets()) {
                $bver = $entity->local_non_chauffe()->performance()->b(isolation_paroi: $entity->est_isole());
                $t = $entity->local_non_chauffe()->ensoleillement()->find($mois)->t;
                $sst = $entity->local_non_chauffe()->ensoleillement()->find($mois)->sst;
                $ssd = $this->ssd(t: $t, surface: $entity->surface_deperditive(), sw: $sw, fe: $fe, c1: $c1);
                $ssind = $this->ssind(sst: $sst, ssd: $ssd);
                $sse = $this->sse_veranda(ssd: $ssd, ssind: $ssind, bver: $bver);
            }
            $collection[] = Ensoleillement::create(mois: $mois, fe: $fe, sw: $sw, c1: $c1, sse: $sse);
        }
        return new EnsoleillementCollection($collection);
    }

    private function calcule_fe1(Baie $entity): float
    {
        /** @var float[] */
        $fe1_collection = $entity->masques_proches()->map(fn(MasqueProche $item) => $this->fe1(
            type_masque_proche: $item->type_masque(),
            avancee_masque: $item->avancee(),
            orientation_baie: $entity->orientation(),
        ))->values();

        return \count($fe1_collection) ? \min($fe1_collection) : 1;
    }

    private function calcule_fe2(Baie $entity): float
    {
        /** @var float[] */
        $fe2_collection = $entity->masques_lointains()
            ->filter_by_type(TypeMasqueLointain::MASQUE_LOINTAIN_HOMOGENE)
            ->map(fn(MasqueLointain $item) => $this->fe2(
                type_masque_lointain: $item->type_masque(),
                orientation_baie: $item->orientation(),
                hauteur_masque: $item->hauteur(),
            ))->values();

        return \count($fe2_collection) ? \min($fe2_collection) : 1;
    }

    private function calcule_omb(Baie $entity): float
    {
        if (null === $orientation_baie = $entity->orientation())
            return 0;

        $masques_non_homogenes = $entity->masques_lointains()->filter_by_type(
            type_masque: TypeMasqueLointain::MASQUE_LOINTAIN_NON_HOMOGENE
        );
        /** @var float[] */
        $omb_collection = [];

        foreach (SecteurChampsVision::secteurs_by_orientation(Orientation::from_azimut($orientation_baie)) as $secteur) {
            $masques = $masques_non_homogenes->filter_by_secteur(secteur: $secteur);
            foreach ($masques as $masque) {
                $omb = $this->omb(
                    type_masque_lointain: $masque->type_masque(),
                    secteur: $secteur,
                    orientation_baie: $orientation_baie,
                    hauteur_masque: $masque->hauteur(),
                );

                if (\array_key_exists($secteur->value, $omb_collection)) {
                    $omb_collection[$secteur->value] = \max($omb_collection[$secteur->value], $omb);
                } else {
                    $omb_collection[$secteur->value] = $omb;
                }
            }
        }

        return \count($omb_collection) ? \min(\array_sum($omb_collection), 100) : 0;
    }

    private function calcule_sw(Baie $entity): float
    {
        return $this->sw_final(
            sw1: $this->sw(
                type_baie: $entity->caracteristique()->type,
                presence_soubassement: $entity->caracteristique()->presence_soubassement,
                nature_menuiserie: $entity->caracteristique()->menuiserie?->nature,
                type_vitrage: $entity->caracteristique()->menuiserie?->type_vitrage,
                type_pose: $entity->caracteristique()->menuiserie?->type_pose,
                sw_saisi: $entity->caracteristique()->sw,
            ),
            sw2: $entity->double_fenetre() ? $this->sw(
                type_baie: $entity->double_fenetre()->type,
                presence_soubassement: $entity->double_fenetre()->presence_soubassement,
                nature_menuiserie: $entity->double_fenetre()->menuiserie?->nature,
                type_vitrage: $entity->double_fenetre()->menuiserie?->type_vitrage,
                type_pose: $entity->double_fenetre()->menuiserie?->type_pose,
                sw_saisi: $entity->double_fenetre()->sw,
            ) : null,
        );
    }

    public function sse(float $surface, float $sw, float $fe, float $c1): float
    {
        return $surface * $sw * $fe * $c1;
    }

    public function sse_veranda(float $ssd, float $ssind, float $bver): float
    {
        return $ssd + $ssind * $bver;
    }

    public function ssind(float $sst, float $ssd): float
    {
        return $sst - $ssd;
    }

    public function ssd(float $t, float $surface, float $sw, float $fe, float $c1)
    {
        return $t * $surface * $sw * $fe * $c1;
    }

    public function sw_final(float $sw1, ?float $sw2): float
    {
        return $sw2 ? $sw1 * $sw2 : $sw1;
    }

    public function sw(
        TypeBaie $type_baie,
        ?bool $presence_soubassement,
        ?NatureMenuiserie $nature_menuiserie,
        ?TypeVitrage $type_vitrage,
        ?TypePose $type_pose,
        ?float $sw_saisi,
    ): float {
        if ($sw_saisi)
            return $sw_saisi;

        if (null === $sw = $this->sw_repository->find_by(
            type_baie: $type_baie,
            presence_soubassement: $presence_soubassement,
            nature_menuiserie: $nature_menuiserie,
            type_vitrage: $type_vitrage,
            type_pose: $type_pose,
        )) throw new \DomainException('Valeur forfaitaire Sw non trouvée');

        return $sw->sw;
    }

    public function c1_collection(ZoneClimatique $zone_climatique, int $inclinaison, ?Orientation $orientation): C1Collection
    {
        $collection = $this->c1_repository->search_by(
            zone_climatique: $zone_climatique,
            inclinaison: $inclinaison,
            orientation: $orientation,
        );

        if (false === $collection->est_valide())
            throw new \DomainException('Valeurs forfaitaires C1 non trouvées');

        return $collection;
    }

    public function fe(float $fe1, float $fe2, float $omb): float
    {
        $omb = \min(100, $omb);
        $fe2 = \min($fe2, 1 - $omb / 100);

        return $fe1 * $fe2;
    }

    public function fe1(
        TypeMasqueProche $type_masque_proche,
        ?float $avancee_masque,
        ?float $orientation_baie,
    ): float {
        if (null === $fe1 = $this->fe1_repository->find_by(
            type_masque_proche: $type_masque_proche,
            avancee_masque: $avancee_masque,
            orientation_baie: $orientation_baie,
        )) throw new \DomainException('Valeur forfaitaire Fe1 non trouvée');

        return $fe1->fe1;
    }

    public function fe2(
        TypeMasqueLointain $type_masque_lointain,
        float $orientation_baie,
        float $hauteur_masque,
    ): float {
        if (null === $fe2 = $this->fe2_repository->find_by(
            type_masque_lointain: $type_masque_lointain,
            orientation_baie: Orientation::from_azimut($orientation_baie),
            hauteur_masque_alpha: $hauteur_masque,
        )) throw new \DomainException('Valeur forfaitaire Fe2 non trouvée');

        return $fe2->fe2;
    }

    public function omb(
        TypeMasqueLointain $type_masque_lointain,
        SecteurChampsVision $secteur,
        float $orientation_baie,
        float $hauteur_masque,
    ): float {
        if (null === $omb = $this->omb_repository->find_by(
            type_masque_lointain: $type_masque_lointain,
            orientation_baie: Orientation::from_azimut($orientation_baie),
            secteur: $secteur,
            hauteur_masque_alpha: $hauteur_masque,
        )) throw new \DomainException('Valeur forfaitaire Omb non trouvée');

        return $omb->omb;
    }
}
