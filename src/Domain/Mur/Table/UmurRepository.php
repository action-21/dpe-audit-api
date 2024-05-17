<?php

namespace App\Domain\Mur\Table;

use App\Domain\Batiment\Enum\ZoneClimatique;

interface UmurRepository
{
    public function find(int $id): ?Umur;
    public function find_by(
        ZoneClimatique $zone_climatique,
        int $annee_construction_isolation,
        bool $effet_joule,
    ): ?Umur;
}
