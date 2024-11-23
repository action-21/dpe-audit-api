<?php

namespace App\Domain\Ecs\Data;

use App\Domain\Ecs\Enum\TypeChaudiere;

interface PnRepository
{
    public function find_by(TypeChaudiere $type_chaudiere, int $annee_installation, float $pdim): ?Pn;
}
