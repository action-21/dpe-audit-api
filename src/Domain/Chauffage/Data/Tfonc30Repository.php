<?php

namespace App\Domain\Chauffage\Data;

use App\Domain\Chauffage\Enum\{TemperatureDistribution, TypeCombustion, TypeGenerateur};

interface Tfonc30Repository
{
    public function find_by(
        TypeGenerateur $type_generateur,
        TypeCombustion $type_combustion,
        TemperatureDistribution $temperature_distribution,
        int $annee_installation_generateur,
        int $annee_installation_emetteur,
    ): ?Tfonc30;
}
