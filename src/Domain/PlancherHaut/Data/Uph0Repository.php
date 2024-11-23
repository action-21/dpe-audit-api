<?php

namespace App\Domain\PlancherHaut\Data;

use App\Domain\PlancherHaut\Enum\TypePlancherHaut;

interface Uph0Repository
{
    public function find_by(TypePlancherHaut $type_plancher_haut): ?Uph0;
}
