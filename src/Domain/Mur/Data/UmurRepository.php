<?php

namespace App\Domain\Mur\Data;

use App\Domain\Common\Enum\ZoneClimatique;

interface UmurRepository
{
    public function find_by(ZoneClimatique $zone_climatique, int $annee_construction_isolation, bool $effet_joule): ?Umur;
}
