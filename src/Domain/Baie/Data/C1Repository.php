<?php

namespace App\Domain\Baie\Data;

use App\Domain\Common\Enum\{Orientation, ZoneClimatique};

interface C1Repository
{
    public function search_by(ZoneClimatique $zone_climatique, float $inclinaison, ?Orientation $orientation): C1Collection;
}
