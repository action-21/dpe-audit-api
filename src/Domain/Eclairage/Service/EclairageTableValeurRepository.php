<?php

namespace App\Domain\Eclairage\Service;

use App\Domain\Common\Enum\ZoneClimatique;

interface EclairageTableValeurRepository
{
    public function nhecl(ZoneClimatique $zone_climatique): ?float;
}
