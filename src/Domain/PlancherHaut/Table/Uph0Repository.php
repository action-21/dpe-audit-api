<?php

namespace App\Domain\PlancherHaut\Table;

use App\Domain\PlancherHaut\Enum\TypePlancherHaut;

interface Uph0Repository
{
    public function find(int $id): ?Uph0;
    public function find_by(TypePlancherHaut $type_plancher_haut): ?Uph0;
}
