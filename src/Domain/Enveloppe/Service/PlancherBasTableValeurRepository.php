<?php

namespace App\Domain\Enveloppe\Service;

use App\Domain\Common\Enum\ZoneClimatique;
use App\Domain\Common\ValueObject\Annee;
use App\Domain\Enveloppe\Enum\Mitoyennete;
use App\Domain\Enveloppe\Enum\PlancherBas\TypePlancherBas;

interface PlancherBasTableValeurRepository extends ParoiTableValeurRepository
{
    public function u0(?TypePlancherBas $type_structure): ?float;

    public function ue(
        Mitoyennete $mitoyennete,
        Annee $annee_construction,
        float $perimetre,
        float $surface,
        float $u,
    ): ?float;

    public function u(
        ZoneClimatique $zone_climatique,
        Annee $annee_construction_isolation,
        bool $effet_joule,
    ): ?float;
}
