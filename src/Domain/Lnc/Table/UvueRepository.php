<?php

namespace App\Domain\Lnc\Table;

use App\Domain\Lnc\Enum\TypeLnc;

interface UvueRepository
{
    public function find(int $id): ?Uvue;
    public function find_by(TypeLnc $type_lnc): ?Uvue;
}
