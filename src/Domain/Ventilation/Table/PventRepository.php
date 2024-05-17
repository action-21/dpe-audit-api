<?php

namespace App\Domain\Ventilation\Table;

use App\Domain\Batiment\Enum\TypeBatiment;
use App\Domain\Ventilation\Enum\{TypeInstallation, TypeVentilation};

interface PventRepository
{
    public function find(int $id): ?Pvent;

    public function find_by(
        TypeVentilation $type_ventilation,
        ?TypeBatiment $type_batiment,
        ?TypeInstallation $type_installation,
        ?int $annee_installation
    ): ?Pvent;
}
