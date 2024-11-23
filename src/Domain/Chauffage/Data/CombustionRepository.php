<?php

namespace App\Domain\Chauffage\Data;

use App\Domain\Chauffage\Enum\{EnergieGenerateur, TypeGenerateur};

interface CombustionRepository
{
    public function find_by(
        TypeGenerateur $type_generateur,
        EnergieGenerateur $energie_generateur,
        int $annee_installation_generateur,
        float $pn,
    ): ?Combustion;
}
