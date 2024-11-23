<?php

namespace App\Domain\Chauffage\Data;

use App\Domain\Chauffage\Enum\TemperatureDistribution;

interface Tfonc100Repository
{
    public function find_by(
        TemperatureDistribution $temperature_distribution,
        int $annee_installation_emetteur,
    ): ?Tfonc100;
}
