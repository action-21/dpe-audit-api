<?php

namespace App\Domain\Ecs\Data;

use App\Domain\Ecs\Enum\{EnergieGenerateur, TypeCombustion, TypeGenerateur};

interface CombustionRepository
{
    public function find_by(
        TypeGenerateur $type_generateur,
        EnergieGenerateur $energie_generateur,
        ?TypeCombustion $type_combustion,
        int $annee_installation_generateur,
        float $pn,
    ): ?Combustion;
}
