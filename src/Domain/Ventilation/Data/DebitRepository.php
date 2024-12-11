<?php

namespace App\Domain\Ventilation\Data;

use App\Domain\Ventilation\Enum\{TypeGenerateur, TypeVentilation, TypeVmc};

interface DebitRepository
{
    public function find_by(
        TypeVentilation $type_ventilation,
        ?TypeGenerateur $type_generateur,
        ?TypeVmc $type_vmc,
        ?bool $presence_echangeur_thermique,
        ?bool $generateur_collectif,
        ?int $annee_installation,
    ): ?Debit;
}
