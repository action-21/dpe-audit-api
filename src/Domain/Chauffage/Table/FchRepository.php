<?php

namespace App\Domain\Chauffage\Table;

use App\Domain\Batiment\Enum\{TypeBatiment, ZoneClimatique};

interface FchRepository
{
    public function find(int $id): ?Fch;
    public function find_by(ZoneClimatique $zone_climatique, TypeBatiment $type_batiment): ?Fch;
}
