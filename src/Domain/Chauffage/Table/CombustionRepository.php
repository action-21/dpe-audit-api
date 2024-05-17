<?php

namespace App\Domain\Chauffage\Table;

use App\Domain\Chauffage\Enum\TypeGenerateur;

interface CombustionRepository
{
    public function find(int $id): ?Combustion;
    public function find_by(TypeGenerateur $type_generateur, int $annee_installation, ?float $puissance_nominale): ?Combustion;
}
