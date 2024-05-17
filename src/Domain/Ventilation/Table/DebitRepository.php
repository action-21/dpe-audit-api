<?php

namespace App\Domain\Ventilation\Table;

use App\Domain\Ventilation\Enum\{TypeInstallation, TypeVentilation};

interface DebitRepository
{
    public function find(int $id): ?Debit;

    public function find_by(
        TypeVentilation $type_ventilation,
        ?TypeInstallation $type_installation,
        ?int $annee_installation
    ): ?Debit;
}
