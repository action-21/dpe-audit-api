<?php

namespace App\Domain\Chauffage\Table;

use App\Domain\Chauffage\Enum\{TemperatureDistribution};

interface Tfonc100Repository
{
    public function find(int $id): ?Tfonc100;
    public function find_by(TemperatureDistribution $temperature_distribution, int $annee_installation_emetteur): ?Tfonc100;
}
