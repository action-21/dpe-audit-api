<?php

namespace App\Domain\Chauffage\Data;

use App\Domain\Common\Enum\{Enum, ZoneClimatique};

interface FchRepository
{
    public function find_by(Enum $type_batiment, ZoneClimatique $zone_climatique,): ?Fch;
}
