<?php

namespace App\Domain\Chauffage\Data;

use App\Domain\Chauffage\Enum\{TypeEmission, TypeGenerateur};
use App\Domain\Common\Enum\ZoneClimatique;

interface ScopRepository
{
    public function find_by(
        ZoneClimatique $zone_climatique,
        TypeGenerateur $type_generateur,
        TypeEmission $type_emission,
        int $annee_installation_generateur,
    ): ?Scop;
}
