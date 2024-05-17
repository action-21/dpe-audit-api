<?php

namespace App\Domain\Ecs\Table;

use App\Domain\Ecs\Enum\{TypeGenerateur};

interface RgRepository
{
    public function find(int $id): ?Rg;
    public function find_by(TypeGenerateur $type_generateur): ?Rg;
}
