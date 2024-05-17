<?php

namespace App\Domain\Ecs\Table;

use App\Domain\Ecs\Enum\TypeGenerateur;

interface CombustionRepository
{
    public function find(int $id): ?Combustion;
    public function find_by(TypeGenerateur $type_generateur, int $annee_installation, ?float $puissance_nominale): ?Combustion;
}
