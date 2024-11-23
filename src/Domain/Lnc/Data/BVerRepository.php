<?php

namespace App\Domain\Lnc\Data;

use App\Domain\Common\Enum\ZoneClimatique;

interface BVerRepository
{
    public function search_by(ZoneClimatique $zone_climatique): BverCollection;
}
