<?php

namespace App\Domain\Batiment\Table;

use App\Domain\Batiment\Enum\ZoneClimatique;

interface NheclRepository
{
    public function search(int $id): NheclCollection;
    public function search_by(ZoneClimatique $zone_climatique): NheclCollection;
}
