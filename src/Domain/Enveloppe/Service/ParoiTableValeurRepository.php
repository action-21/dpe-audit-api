<?php

namespace App\Domain\Enveloppe\Service;

use App\Domain\Common\Enum\{Orientation, ZoneClimatique};
use App\Domain\Enveloppe\Enum\{EtatIsolation, Mitoyennete};

interface ParoiTableValeurRepository
{
    public function b(Mitoyennete $mitoyennete): ?float;

    /**
     * @param Orientation[] $orientations_lnc
     */
    public function bver(
        ZoneClimatique $zone_climatique,
        EtatIsolation $isolation_paroi,
        array $orientations_lnc,
    ): ?float;
}
