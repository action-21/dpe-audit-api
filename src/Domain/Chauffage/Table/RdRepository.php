<?php

namespace App\Domain\Chauffage\Table;

use App\Domain\Chauffage\Enum\{TemperatureDistribution, TypeDistribution, TypeInstallation};

interface RdRepository
{
    public function find(int $id): ?Rd;
    public function find_by(
        TypeInstallation $type_installation,
        TypeDistribution $type_distribution,
        ?TemperatureDistribution $temperature_distribution,
        ?bool $reseau_distribution_isole,
    ): ?Rd;
}
