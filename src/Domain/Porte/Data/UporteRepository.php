<?php

namespace App\Domain\Porte\Data;

use App\Domain\Porte\Enum\{EtatIsolation, Materiau, TypeVitrage};

interface UporteRepository
{
    public function find_by(
        bool $presence_sas,
        EtatIsolation $isolation,
        Materiau $materiau,
        ?TypeVitrage $type_vitrage,
        ?float $taux_vitrage,
    ): ?Uporte;
}
