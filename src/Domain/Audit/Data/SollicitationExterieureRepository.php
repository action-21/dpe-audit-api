<?php

namespace App\Domain\Audit\Data;

use App\Domain\Common\Enum\ZoneClimatique;

interface SollicitationExterieureRepository
{
    public function search_by(
        ZoneClimatique $zone_climatique,
        int $altitude,
        bool $parois_anciennes_lourdes,
    ): SollicitationExterieureCollection;
}
