<?php

namespace App\Domain\Ecs\Data;

use App\Domain\Common\Enum\{Enum, ZoneClimatique};
use App\Domain\Ecs\Enum\UsageEcs;

interface FecsRepository
{
    public function find_by(
        Enum $type_batiment,
        ZoneClimatique $zone_climatique,
        UsageEcs $usage_systeme_solaire,
        int $anciennete_installation,
    ): ?Fecs;
}
