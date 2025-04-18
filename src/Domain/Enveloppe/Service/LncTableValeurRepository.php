<?php

namespace App\Domain\Enveloppe\Service;

use App\Domain\Common\Enum\{Mois, Orientation, ZoneClimatique};
use App\Domain\Common\ValueObject\Inclinaison;
use App\Domain\Enveloppe\Enum\EtatIsolation;
use App\Domain\Enveloppe\Enum\Lnc\{Materiau, TypeBaie, TypeLnc, TypeVitrage};

interface LncTableValeurRepository
{
    public function uvue(TypeLnc $type_lnc): ?float;

    public function b(
        float $uvue,
        EtatIsolation $isolation_aiu,
        EtatIsolation $isolation_aue,
        float $aiu,
        float $aue,
    ): ?float;

    public function c1(
        Mois $mois,
        ZoneClimatique $zone_climatique,
        Inclinaison $inclinaison,
        ?Orientation $orientation,
    ): ?float;

    public function t(
        TypeBaie $type_baie,
        ?Materiau $materiau,
        ?bool $presence_rupteur_pont_thermique,
        ?TypeVitrage $type_vitrage,
    ): ?float;
}
