<?php

namespace App\Domain\Enveloppe\Service;

use App\Domain\Common\Enum\ZoneClimatique;
use App\Domain\Common\ValueObject\Annee;
use App\Domain\Enveloppe\Enum\PlancherHaut\{Configuration, TypePlancherHaut};

interface PlancherHautTableValeurRepository extends ParoiTableValeurRepository
{
    public function u0(?TypePlancherHaut $type_structure): ?float;

    public function u(
        ZoneClimatique $zone_climatique,
        Configuration $configuration,
        Annee $annee_construction_isolation,
        bool $effet_joule,
    ): ?float;
}
