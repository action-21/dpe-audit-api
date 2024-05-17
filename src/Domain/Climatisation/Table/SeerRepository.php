<?php

namespace App\Domain\Climatisation\Table;

use App\Domain\Batiment\Enum\ZoneClimatique;

interface SeerRepository
{
    public function find(int $id): ?Seer;
    public function find_by(ZoneClimatique $zone_climatique, int $annee_installation): ?Seer;
}
