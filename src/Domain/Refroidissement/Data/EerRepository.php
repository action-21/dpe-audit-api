<?php

namespace App\Domain\Refroidissement\Data;

use App\Domain\Common\Enum\ZoneClimatique;

interface EerRepository
{
    public function find_by(ZoneClimatique $zone_climatique, int $annee_installation_generateur): ?Eer;
}
