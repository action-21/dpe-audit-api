<?php

namespace App\Domain\Chauffage\Table;

use App\Domain\Batiment\Enum\ZoneClimatique;
use App\Domain\Chauffage\Enum\{TypeEmission, TypeGenerateur};

interface ScopRepository
{
    public function find(int $id): ?Scop;
    public function find_by(
        ZoneClimatique $zone_climatique,
        TypeGenerateur $type_generateur,
        ?TypeEmission $type_emission,
        ?int $anne_installation_generateur,
    ): ?Scop;
}
