<?php

namespace App\Domain\Chauffage\Data;

use App\Domain\Chauffage\Enum\{LabelGenerateur, TypeEmission, TypeGenerateur};

interface ReRepository
{
    public function find_by(
        TypeEmission $type_emission,
        TypeGenerateur $type_generateur,
        ?LabelGenerateur $label_generateur,
    ): ?Re;
}
