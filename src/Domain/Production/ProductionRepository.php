<?php

namespace App\Domain\Production;

use App\Domain\Common\ValueObject\Id;

interface ProductionRepository
{
    public function find(Id $id): ?Production;
}
