<?php

namespace App\Domain\Ecs\Table;

use App\Domain\Batiment\Enum\ZoneClimatique;
use App\Domain\Ecs\Enum\TypeInstallation;

interface CopRepository
{
    public function find(int $id): ?Cop;
    public function find_by(ZoneClimatique $zone_climatique, TypeInstallation $type_installation, int $annee_installation): ?Cop;
}
