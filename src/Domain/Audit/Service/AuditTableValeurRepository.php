<?php

namespace App\Domain\Audit\Service;

use App\Domain\Audit\Enum\Etiquette;
use App\Domain\Audit\ValueObject\SollicitationsExterieures;
use App\Domain\Common\Enum\ZoneClimatique;

interface AuditTableValeurRepository
{
    public function sollicitations_exterieures(
        ZoneClimatique $zone_climatique,
        int|float $altitude,
        bool $parois_anciennes_lourdes,
    ): ?SollicitationsExterieures;

    public function tbase(
        ZoneClimatique $zone_climatique,
        int|float $altitude,
    ): ?float;

    public function etiquette_energie(
        ZoneClimatique $zone_climatique,
        int|float $altitude,
        float $cep,
        float $eges,
    ): ?Etiquette;

    public function etiquette_climat(
        ZoneClimatique $zone_climatique,
        int|float $altitude,
        float $eges,
    ): ?Etiquette;
}
