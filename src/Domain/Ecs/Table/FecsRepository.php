<?php

namespace App\Domain\Ecs\Table;

use App\Domain\Batiment\Enum\{TypeBatiment, ZoneClimatique};
use App\Domain\Ecs\Enum\TypeInstallationSolaire;

interface FecsRepository
{
    public function find(int $id): ?Fecs;
    public function find_by(
        ZoneClimatique $zone_climatique,
        TypeBatiment $type_batiment,
        TypeInstallationSolaire $type_installation_solaire,
    ): ?Fecs;
}
