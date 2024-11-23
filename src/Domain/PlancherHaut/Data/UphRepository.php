<?php

namespace App\Domain\PlancherHaut\Data;

use App\Domain\Common\Enum\ZoneClimatique;
use App\Domain\PlancherHaut\Enum\Categorie;

interface UphRepository
{
    public function find_by(
        ZoneClimatique $zone_climatique,
        Categorie $categorie,
        int $annee_construction_isolation,
        bool $effet_joule,
    ): ?Uph;
}
