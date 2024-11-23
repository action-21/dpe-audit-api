<?php

namespace App\Domain\Audit;

use App\Domain\Common\Type\Id;

interface AuditRepository
{
    public function find(Id $id): ?Audit;
}
