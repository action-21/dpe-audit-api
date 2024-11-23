<?php

namespace App\Domain\Production;

use App\Domain\Common\Type\Id;

interface ProductionRepository
{
    public function find(Id $audit_id): ?Production;
}
