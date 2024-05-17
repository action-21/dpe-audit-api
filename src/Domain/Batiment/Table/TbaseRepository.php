<?php

namespace App\Domain\Batiment\Table;

use App\Domain\Batiment\Enum\ZoneClimatique;

interface TbaseRepository
{
    public function find(int $id): ?Tbase;
    public function find_by(ZoneClimatique $zone_climatique, int $altitude): ?Tbase;
}
