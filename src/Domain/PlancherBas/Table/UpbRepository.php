<?php

namespace App\Domain\PlancherBas\Table;

use App\Domain\Batiment\Enum\ZoneClimatique;

interface UpbRepository
{
    public function find(int $id): ?Upb;
    public function find_by(
        ZoneClimatique $zone_climatique,
        int $annee_construction_isolation,
        bool $effet_joule,
    ): ?Upb;
}
