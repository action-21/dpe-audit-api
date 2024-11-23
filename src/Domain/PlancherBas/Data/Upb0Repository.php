<?php

namespace App\Domain\PlancherBas\Data;

use App\Domain\PlancherBas\Enum\TypePlancherBas;

interface Upb0Repository
{
    public function find_by(TypePlancherBas $type_plancher_bas): ?Upb0;
}
