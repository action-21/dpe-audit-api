<?php

namespace App\Domain\Chauffage\Data;

use App\Domain\Chauffage\Enum\{IsolationReseau, TemperatureDistribution, TypeDistribution};

interface RdRepository
{
    public function find_by(
        ?TypeDistribution $type_distribution,
        ?TemperatureDistribution $temperature_distribution,
        ?IsolationReseau $isolation_reseau,
        ?bool $reseau_collectif,
    ): ?Rd;
}
