<?php

namespace App\Domain\Simulation\Data;

use App\Domain\Common\Enum\ZoneClimatique;
use App\Domain\Simulation\Enum\Etiquette;

interface EtiquetteEnergieRepository
{
    public function find(ZoneClimatique $zone_climatique, int $altitude, float $cep, float $eges,): ?Etiquette;
}
