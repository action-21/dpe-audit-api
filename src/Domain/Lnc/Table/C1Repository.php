<?php

namespace App\Domain\Lnc\Table;

use App\Domain\Batiment\Enum\ZoneClimatique;
use App\Domain\Common\Enum\Orientation;

interface C1Repository
{
    public function search(int $id): C1Collection;
    public function search_by(ZoneClimatique $zone_climatique, int $inclinaison, ?Orientation $orientation): C1Collection;
}
