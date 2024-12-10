<?php

namespace App\Domain\Chauffage\Data;

use App\Domain\Chauffage\Enum\{EnergieGenerateur, TypeCombustion, TypeGenerateur};

interface CombustionRepository
{
    public function find_by(
        TypeGenerateur $type_generateur,
        ?TypeCombustion $type_combustion,
        EnergieGenerateur $energie_generateur,
        int $annee_installation_generateur,
        float $pn,
    ): ?Combustion;
}
