<?php

namespace App\Domain\Lnc\Data;

use App\Domain\Lnc\Enum\TypeLnc;

interface UvueRepository
{
    public function find_by(TypeLnc $type_lnc): ?Uvue;
}
