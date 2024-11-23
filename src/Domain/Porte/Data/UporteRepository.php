<?php

namespace App\Domain\Porte\Data;

use App\Domain\Porte\Enum\{EtatIsolation, NatureMenuiserie, TypeVitrage};

interface UporteRepository
{
    public function find_by(
        bool $presence_sas,
        EtatIsolation $isolation,
        NatureMenuiserie $nature_menuiserie,
        ?TypeVitrage $type_vitrage,
        ?float $taux_vitrage,
    ): ?Uporte;
}
