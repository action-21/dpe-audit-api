<?php

namespace App\Domain\Audit;

use App\Domain\Common\ValueObject\Id;

interface AuditRepository
{
    public function find(Id $id, bool $eager = false): ?Audit;
    public function save(Audit $audit): void;
    public function remove(Audit $audit): void;
}
