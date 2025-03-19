<?php

namespace App\Domain\Lnc\Service;

use App\Domain\Common\Enum\{Mois, Orientation, ZoneClimatique};
use App\Domain\Common\Service\MoteurCalcul;
use App\Domain\Lnc\Data\{C1Collection, C1Repository, TRepository};
use App\Domain\Lnc\Entity\Baie;
use App\Domain\Lnc\Enum\{Materiau, Mitoyennete, TypeBaie, TypeLnc, TypeVitrage};
use App\Domain\Lnc\ValueObject\{EnsoleillementBaie, EnsoleillementBaieItem};

final class MoteurEnsoleillementBaie extends MoteurCalcul
{
    public function __construct(
        private C1Repository $c1_repository,
        private TRepository $t_repository,
    ) {}

    /**
     * Surface sud équivalente des apports totaux dans la véranda par la baie (m²)
     */
    public function sst(float $surface, float $t, float $fe, float $c1): float
    {
        return $surface * (0.8 * $t + 0.024) * $fe * $c1;
    }

    public function fe(): float
    {
        return 1;
    }

    public function c1(ZoneClimatique $zone_climatique, float $inclinaison, ?Orientation $orientation): C1Collection
    {
        $collection = $this->c1_repository->search_by(
            zone_climatique: $zone_climatique,
            inclinaison: $inclinaison,
            orientation: $orientation,
        );

        if (false === $collection->est_valide())
            throw new \DomainException("Valeurs forfaitaires C1 non trouvées");

        return $collection;
    }

    public function t(
        TypeBaie $type_baie,
        ?Materiau $materiau,
        ?TypeVitrage $type_vitrage,
        ?bool $presence_rupteur_pont_thermique,
    ): float {
        if ($type_baie !== TypeBaie::POLYCARBONATE) {
            if (null === $materiau) {
                $materiau = Materiau::PVC;
                $this->valeurs_forfaitaires()->add('materiau');
            }
            if (null === $type_vitrage) {
                $type_vitrage = TypeVitrage::SIMPLE_VITRAGE;
                $this->valeurs_forfaitaires()->add('type_vitrage');
            }
        }

        if ($materiau === Materiau::METAL && null === $presence_rupteur_pont_thermique) {
            $presence_rupteur_pont_thermique = false;
            $this->valeurs_forfaitaires()->add('presence_rupteur_pont_thermique');
        }

        if (null === $valeur = $this->t_repository->find_by(
            type_baie: $type_baie,
            materiau: $materiau,
            type_vitrage: $type_vitrage,
            presence_rupteur_pont_thermique: $presence_rupteur_pont_thermique
        )) throw new \DomainException("Valeur forfaitaire t non trouvée");

        return $valeur->t;
    }

    public function __invoke(Baie $entity): EnsoleillementBaie
    {
        $this->valeurs_forfaitaires()->reset();

        if ($entity->local_non_chauffe()->type() !== TypeLnc::ESPACE_TAMPON_SOLARISE) {
            return new EnsoleillementBaie();
        }
        if ($entity->position()->mitoyennete !== Mitoyennete::EXTERIEUR) {
            return new EnsoleillementBaie();
        }

        $c1_collection = $this->c1(
            zone_climatique: $entity->local_non_chauffe()->zone_climatique(),
            inclinaison: $entity->position()->inclinaison,
            orientation: $entity->position()->orientation ? Orientation::from_azimut($entity->position()->orientation) : null,
        );
        $t = $this->t(
            type_baie: $entity->type(),
            materiau: $entity->materiau(),
            type_vitrage: $entity->type_vitrage(),
            presence_rupteur_pont_thermique: $entity->presence_rupteur_pont_thermique(),
        );

        return EnsoleillementBaie::create(function (Mois $mois) use ($t, $c1_collection, $entity) {
            return EnsoleillementBaieItem::create(
                mois: $mois,
                fe: ($fe = $this->fe()),
                t: $t,
                c1: ($c1 = $c1_collection->find($mois)->c1),
                sst: $this->sst(surface: $entity->position()->surface, t: $t, fe: $fe, c1: $c1,)
            );
        });
    }
}
