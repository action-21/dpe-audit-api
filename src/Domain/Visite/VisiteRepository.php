<?php

namespace App\Domain\Visite;

use App\Domain\Common\ValueObject\Id;

interface VisiteRepository
{
    public function find(Id $audit_id): ?Visite;
}
