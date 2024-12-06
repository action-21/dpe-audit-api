<?php

namespace App\Domain\Ecs\Data;

use App\Domain\Ecs\Enum\{EnergieGenerateur, TypeGenerateur};

interface RgRepository
{
    public function find_by(TypeGenerateur $type_generateur, EnergieGenerateur $energie_generateur,): ?Rg;
}
