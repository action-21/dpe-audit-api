<?php

namespace App\Domain\Ventilation\Data;

use App\Domain\Ventilation\Enum\{TypeGenerateur, TypeVentilation, TypeVmc};

interface PventRepository
{
    public function find_by(
        TypeVentilation $type_ventilation,
        ?TypeGenerateur $type_generateur,
        ?TypeVmc $type_vmc,
        ?int $annee_installation,
        ?bool $generateur_collectif,
    ): ?Pvent;
}
