<?php

namespace App\Domain\Ecs\Data;

use App\Domain\Ecs\Enum\{LabelGenerateur, TypeGenerateur};

interface CrRepository
{
    public function find_by(TypeGenerateur $type_generateur, int $volume_stockage, ?LabelGenerateur $label_generateur,): ?Cr;
}
