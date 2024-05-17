<?php

namespace App\Domain\Chauffage\Table;

use App\Domain\Chauffage\Enum\{TypeDistribution, TypeEmission, TypeGenerateur, TypeInstallation};

interface RrRepository
{
    public function find(int $id): ?Rr;
    public function find_by(
        TypeInstallation $type_installation,
        TypeEmission $type_emission,
        TypeDistribution $type_distribution,
        TypeGenerateur $type_generateur,
    ): ?Rr;
}
