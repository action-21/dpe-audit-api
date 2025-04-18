<?php

namespace App\Domain\Enveloppe\Service;

use App\Domain\Enveloppe\Enum\EtatIsolation;
use App\Domain\Enveloppe\Enum\Porte\{Materiau, TypeVitrage};

interface PorteTableValeurRepository extends ParoiTableValeurRepository
{
    public function u(
        bool $presence_sas,
        EtatIsolation $isolation,
        Materiau $materiau,
        ?TypeVitrage $type_vitrage,
        ?float $taux_vitrage,
    ): ?float;
}
