<?php

namespace App\Database\Local\Enveloppe;

use App\Domain\Common\Enum\ZoneClimatique;
use App\Domain\Common\ValueObject\Annee;
use App\Domain\Enveloppe\Enum\PlancherHaut\{Configuration, TypePlancherHaut};
use App\Domain\Enveloppe\Service\PlancherHautTableValeurRepository;

final class XMLPlancherHautTableValeurRepository extends XMLParoiTableValeurRepository implements PlancherHautTableValeurRepository
{
    public function u0(?TypePlancherHaut $type_structure): ?float
    {
        return $this->db->repository('plancher_haut.u0')
            ->createQuery()
            ->and('type_structure', $type_structure)
            ->getOne()
            ?->floatval('u0');
    }

    public function u(
        ZoneClimatique $zone_climatique,
        Configuration $configuration,
        Annee $annee_construction_isolation,
        bool $effet_joule,
    ): ?float {
        return $this->db->repository('plancher_haut.u')
            ->createQuery()
            ->and('zone_climatique', $zone_climatique->code())
            ->and('configuration', $configuration)
            ->and('effet_joule', $effet_joule)
            ->andCompareTo('annee_construction_isolation', $annee_construction_isolation->value())
            ->getOne()
            ?->floatval('u');
    }
}
