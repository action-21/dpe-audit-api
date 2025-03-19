<?php

namespace App\Domain\Audit;

use App\Domain\Common\ValueObject\Id;

interface AuditRepository
{
    public function find(Id $id): ?Audit;
}
