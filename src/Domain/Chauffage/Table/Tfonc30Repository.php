<?php

namespace App\Domain\Chauffage\Table;

use App\Domain\Chauffage\Enum\{TemperatureDistribution, TypeGenerateur};

interface Tfonc30Repository
{
    public function find(int $id): ?Tfonc30;
    public function find_by(
        TypeGenerateur $type_generateur,
        TemperatureDistribution $temperature_distribution,
        int $annee_installation_emetteur,
    ): ?Tfonc30;
}
