<?php

namespace App\Domain\Lnc\Table;

use App\Domain\Batiment\Enum\ZoneClimatique;

interface BVerRepository
{
    public function search(int $id): BverCollection;
    public function search_by(ZoneClimatique $zone_climatique, bool $isolation_aiu): BVerCollection;
}
