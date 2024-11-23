<?php

namespace App\Domain\Audit\Data;

use App\Domain\Common\Enum\ZoneClimatique;

interface TbaseRepository
{
    public function find_by(ZoneClimatique $zone_climatique, int $altitude): ?Tbase;
}
