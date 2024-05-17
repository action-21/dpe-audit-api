<?php

namespace App\Domain\Baie\Table;

use App\Domain\Common\Enum\Orientation;
use App\Domain\Batiment\Enum\ZoneClimatique;

interface C1Repository
{
    public function search(int $id): C1Collection;
    public function search_by(ZoneClimatique $zone_climatique, int $inclinaison, ?Orientation $orientation): C1Collection;
}
