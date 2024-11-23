<?php

namespace App\Domain\PlancherBas\Data;

use App\Domain\Common\Enum\ZoneClimatique;

interface UpbRepository
{
    public function find_by(ZoneClimatique $zone_climatique, int $annee_construction_isolation, bool $effet_joule): ?Upb;
}
