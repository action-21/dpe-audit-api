<?php

namespace App\Domain\Ecs\Data;

use App\Domain\Common\Enum\ZoneClimatique;
use App\Domain\Ecs\Enum\TypeGenerateur;

interface CopRepository
{
    public function find_by(
        ZoneClimatique $zone_climatique,
        TypeGenerateur $type_generateur,
        int $annee_installation,
    ): ?Cop;
}
