<?php

namespace App\Domain\Refroidissement\Service;

use App\Domain\Common\Enum\ZoneClimatique;
use App\Domain\Common\ValueObject\Annee;

interface RefroidissementTableValeurRepository
{
    public function eer(
        ZoneClimatique $zone_climatique,
        Annee $annee_installation_generateur,
    ): ?float;
}
