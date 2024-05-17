<?php

namespace App\Domain\Batiment\Table;

use App\Domain\Batiment\Enum\{ClasseAltitude, ZoneClimatique};

interface SollicitationExterieureRepository
{
    public function search(int $id): SollicitationExterieureCollection;
    public function search_by(
        ZoneClimatique $zone_climatique,
        int $altitude,
        bool $parois_anciennes_lourdes,
    ): SollicitationExterieureCollection;
}
