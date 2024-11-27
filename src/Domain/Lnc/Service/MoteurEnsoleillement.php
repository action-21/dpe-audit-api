<?php

namespace App\Domain\Lnc\Service;

use App\Domain\Common\Enum\{Mois, Orientation, ZoneClimatique};
use App\Domain\Lnc\Data\{C1Collection, C1Repository, TRepository};
use App\Domain\Lnc\Entity\Baie;
use App\Domain\Lnc\Lnc;
use App\Domain\Lnc\Enum\{Mitoyennete, NatureMenuiserie, TypeBaie, TypeLnc, TypeVitrage};
use App\Domain\Lnc\ValueObject\{Ensoleillement, EnsoleillementCollection};
use App\Domain\Lnc\ValueObject\{EnsoleillementBaie, EnsoleillementBaieCollection};

final class MoteurEnsoleillement
{
    public function __construct(
        private C1Repository $c1_repository,
        private TRepository $t_repository,
    ) {}

    public function calcule_ensoleillement(Lnc $entity): EnsoleillementCollection
    {
        $entity->baies()->calcule_ensoleillement($this);

        return EnsoleillementCollection::create(function (Mois $mois) use ($entity) {
            return Ensoleillement::create(
                mois: $mois,
                t: $entity->baies()->t($mois),
                sst: $entity->baies()->sst($mois),
            );
        });
    }

    public function calcule_ensoleillement_baie(Baie $entity): ?EnsoleillementBaieCollection
    {
        if ($entity->type_lnc() !== TypeLnc::ESPACE_TAMPON_SOLARISE)
            return null;
        if ($entity->position()->mitoyennete !== Mitoyennete::EXTERIEUR)
            return null;

        $c1_collection = $this->c1(
            zone_climatique: $entity->local_non_chauffe()->zone_climatique(),
            inclinaison: $entity->inclinaison(),
            orientation: $entity->position()->orientation ? Orientation::from_azimut($entity->position()->orientation) : null,
        );
        $t = $this->t(
            type_baie: $entity->type(),
            nature_menuiserie: $entity->menuiserie()->nature_menuiserie,
            type_vitrage: $entity->menuiserie()->type_vitrage,
            presence_rupteur_pont_thermique: $entity->menuiserie()->presence_rupteur_pont_thermique,
        );

        return EnsoleillementBaieCollection::create(function (Mois $mois) use ($t, $c1_collection, $entity) {
            return EnsoleillementBaie::create(
                mois: $mois,
                fe: ($fe = $this->fe()),
                t: $t,
                c1: ($c1 = $c1_collection->find($mois)->c1),
                sst: $this->sst(surface: $entity->surface(), t: $t, fe: $fe, c1: $c1,)
            );
        });
    }

    public function calcule_coefficient_transparence(Baie $entity): float
    {
        return $this->t(
            type_baie: $entity->type(),
            nature_menuiserie: $entity->menuiserie()->nature_menuiserie,
            type_vitrage: $entity->menuiserie()->type_vitrage,
            presence_rupteur_pont_thermique: $entity->menuiserie()->presence_rupteur_pont_thermique,
        );
    }

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
        ?NatureMenuiserie $nature_menuiserie,
        ?TypeVitrage $type_vitrage,
        ?bool $presence_rupteur_pont_thermique,
    ): float {
        if (null === $valeur = $this->t_repository->find_by(
            type_baie: $type_baie,
            nature_menuiserie: $nature_menuiserie,
            type_vitrage: $type_vitrage,
            presence_rupteur_pont_thermique: $presence_rupteur_pont_thermique
        )) throw new \DomainException("Valeur forfaitaire t non trouvée");

        return $valeur->t;
    }
}
