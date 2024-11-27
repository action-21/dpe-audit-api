<?php

namespace App\Domain\Baie\Service;

use App\Domain\Baie\Baie;
use App\Domain\Baie\Data\{BbaieRepository, DeltarRepository, UgRepository, UjnRepository, UwRepository};
use App\Domain\Baie\Enum\{Mitoyennete, NatureGazLame, NatureMenuiserie, TypeBaie, TypeFermeture, TypeSurvitrage, TypeVitrage};
use App\Domain\Baie\ValueObject\Performance;
use App\Domain\Common\Service\Interpolation;

final class MoteurPerformance
{
    public final const EPAISSEUR_LAME_AIR_DEFAUT = 6;

    public function __construct(
        private BbaieRepository $b_repository,
        private UgRepository $ug_repository,
        private UwRepository $uw_repository,
        private DeltarRepository $deltar_repository,
        private UjnRepository $ujn_repository,
        private Interpolation $interpolation,
    ) {}

    public function calcule_performance(Baie $entity): ?Performance
    {
        if ($entity->mitoyennete() === Mitoyennete::LOCAL_RESIDENTIEL)
            return null;

        $b = $this->b(
            mitoyennete: $entity->mitoyennete(),
            b_lnc: $entity->local_non_chauffe()?->performance()->b(isolation_paroi: $entity->est_isole())
        );
        $ug1 = $this->ug(
            type_baie: $entity->caracteristique()->type,
            type_vitrage: $entity->caracteristique()->menuiserie?->type_vitrage,
            inclinaison: $entity->caracteristique()->inclinaison,
            type_survitrage: $entity->caracteristique()->menuiserie?->survitrage?->type_survitrage,
            nature_gaz_lame: $entity->caracteristique()->menuiserie?->nature_gaz_lame,
            epaisseur_lame_air: $entity->caracteristique()->menuiserie?->epaisseur_lame,
            ug_saisi: $entity->caracteristique()->ug,
        );
        $ug2 = $entity->double_fenetre() ? $this->ug(
            type_baie: $entity->double_fenetre()->type,
            type_vitrage: $entity->double_fenetre()->menuiserie?->type_vitrage,
            inclinaison: $entity->caracteristique()->inclinaison,
            type_survitrage: $entity->double_fenetre()->menuiserie?->survitrage?->type_survitrage,
            nature_gaz_lame: $entity->double_fenetre()->menuiserie?->nature_gaz_lame,
            epaisseur_lame_air: $entity->double_fenetre()->menuiserie?->epaisseur_lame,
            ug_saisi: $entity->double_fenetre()->ug,
        ) : null;
        $uw1 = $this->uw(
            ug: $ug1,
            type_baie: $entity->caracteristique()->type,
            nature_menuiserie: $entity->caracteristique()->menuiserie?->nature,
            presence_soubassement: $entity->caracteristique()->presence_soubassement,
            presence_rupteur_pont_thermique: $entity->caracteristique()->menuiserie?->presence_rupteur_pont_thermique,
            uw_saisi: $entity->caracteristique()->uw,
        );
        $uw2 = $entity->double_fenetre() ? $this->uw(
            ug: $ug2,
            type_baie: $entity->double_fenetre()->type,
            nature_menuiserie: $entity->double_fenetre()->menuiserie?->nature,
            presence_soubassement: $entity->double_fenetre()->presence_soubassement,
            presence_rupteur_pont_thermique: $entity->double_fenetre()->menuiserie?->presence_rupteur_pont_thermique,
            uw_saisi: $entity->double_fenetre()->uw,
        ) : null;
        $uw = $this->uw_final(uw1: $uw1, uw2: $uw2);
        $ujn = $this->ujn(
            uw: $uw,
            type_fermeture: $entity->caracteristique()->type_fermeture,
            ujn_saisi: $entity->caracteristique()->ujn,
        );
        $dp = $this->dp(
            mitoyennete: $entity->mitoyennete(),
            sdep: $entity->surface_deperditive(),
            b: $b,
            u: $ujn,
        );

        return Performance::create(u: $ujn, b: $b, dp: $dp);
    }

    public function dp(Mitoyennete $mitoyennete, float $sdep, float $b, float $u): float
    {
        return $mitoyennete === Mitoyennete::LOCAL_RESIDENTIEL ? 0 : $sdep * $b * $u;
    }

    public function ug(
        TypeBaie $type_baie,
        ?TypeVitrage $type_vitrage,
        ?float $inclinaison,
        ?TypeSurvitrage $type_survitrage,
        ?NatureGazLame $nature_gaz_lame,
        ?float $epaisseur_lame_air,
        ?float $ug_saisi,
    ): float {
        if ($ug_saisi)
            return $ug_saisi;

        if (($type_survitrage || $type_vitrage !== TypeVitrage::SIMPLE_VITRAGE) && null === $epaisseur_lame_air)
            $epaisseur_lame_air = self::EPAISSEUR_LAME_AIR_DEFAUT;

        $collection = $this->ug_repository->search_by(
            type_baie: $type_baie,
            type_vitrage: $type_vitrage,
            type_survitrage: $type_survitrage,
            nature_gaz_lame: $nature_gaz_lame,
            inclinaison_vitrage: $inclinaison,
        )->valeurs_proches(epaisseur_lame: $epaisseur_lame_air);

        if (0 === $collection->count())
            throw new \DomainException("Valeur forfaitaire Ug non trouvé");

        if (1 === $collection->count())
            return $collection->first()->ug;

        return $this->interpolation->interpolation_lineaire(
            x: $epaisseur_lame_air,
            x1: $collection->first()->epaisseur_lame,
            x2: $collection->last()->epaisseur_lame,
            y1: $collection->first()->ug,
            y2: $collection->last()->ug,
        );
    }

    public function uw_final(float $uw1, ?float $uw2): float
    {
        return $uw2 ? 1 / ((1 / $uw1) + (1 / $uw2) + 0.07) : $uw1;
    }

    public function uw(
        float $ug,
        TypeBaie $type_baie,
        ?NatureMenuiserie $nature_menuiserie,
        ?bool $presence_soubassement,
        ?bool $presence_rupteur_pont_thermique,
        ?float $uw_saisi,
    ): float {
        if ($uw_saisi)
            return $uw_saisi;

        $collection = $this->uw_repository->search_by(
            type_baie: $type_baie,
            nature_menuiserie: $nature_menuiserie,
            presence_soubassement: $presence_soubassement,
            presence_rupteur_pont_thermique: $presence_rupteur_pont_thermique,
        );

        if (0 === $collection->count()) {
            throw new \DomainException("Valeur forfaitaire Uw non trouvé");
        }

        if (1 === $collection->count())
            return $collection->first()->uw;

        $collection = $collection->valeurs_proches(ug: $ug);

        return $this->interpolation->interpolation_lineaire(
            x: $ug,
            x1: $collection->first()->ug,
            x2: $collection->last()->ug,
            y1: $collection->first()->uw,
            y2: $collection->last()->uw,
        );
    }

    public function ujn(float $uw, TypeFermeture $type_fermeture, ?float $ujn_saisi,): float
    {
        if ($ujn_saisi)
            return $ujn_saisi;

        if ($type_fermeture === TypeFermeture::SANS_FERMETURE)
            return $uw;

        if (null === $deltar = $this->deltar_repository->find_by(type_fermeture: $type_fermeture))
            throw new \DomainException("Valeur forfaitaire ΔR non trouvé");

        $collection = $this->ujn_repository->search_by(deltar: $deltar->deltar)->valeurs_proches(uw: $uw);

        if (0 === $collection->count())
            throw new \DomainException("Valeur forfaitaire Ujn non trouvé");

        if (1 === $collection->count())
            return $collection->first()->ujn;

        return $this->interpolation->interpolation_lineaire(
            x: $uw,
            x1: $collection->first()->uw,
            x2: $collection->last()->uw,
            y1: $collection->first()->ujn,
            y2: $collection->last()->ujn,
        );
    }

    public function b(Mitoyennete $mitoyennete, ?float $b_lnc,): float
    {
        if (null !== $b_lnc)
            return $b_lnc;

        if (null === $valeur = $this->b_repository->find_by(mitoyennete: $mitoyennete))
            throw new \DomainException("Valeur forfaitaire b non trouvée");

        return $valeur->b;
    }
}
