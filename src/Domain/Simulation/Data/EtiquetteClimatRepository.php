<?php

namespace App\Domain\Simulation\Data;

use App\Domain\Simulation\Enum\Etiquette;

interface EtiquetteClimatRepository
{
    public function find(float $eges,): ?Etiquette;
}
