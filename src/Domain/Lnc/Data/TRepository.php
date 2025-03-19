<?php

namespace App\Domain\Lnc\Data;

use App\Domain\Lnc\Enum\{Materiau, TypeBaie, TypeVitrage};

interface TRepository
{
    public function find_by(
        TypeBaie $type_baie,
        ?Materiau $materiau,
        ?TypeVitrage $type_vitrage,
        ?bool $presence_rupteur_pont_thermique,
    ): ?T;
}
