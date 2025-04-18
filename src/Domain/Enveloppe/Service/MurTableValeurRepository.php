<?php

namespace App\Domain\Enveloppe\Service;

use App\Domain\Common\Enum\ZoneClimatique;
use App\Domain\Common\ValueObject\Annee;
use App\Domain\Enveloppe\Enum\Mur\TypeMur;

interface MurTableValeurRepository extends ParoiTableValeurRepository
{
    public function u0(
        Annee $annee_construction,
        ?TypeMur $type_structure,
        ?float $epaisseur_structure,
    ): ?float;

    public function u(
        ZoneClimatique $zone_climatique,
        Annee $annee_construction_isolation,
        bool $effet_joule,
    ): ?float;
}
