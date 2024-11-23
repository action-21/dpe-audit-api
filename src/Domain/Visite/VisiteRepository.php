<?php

namespace App\Domain\Visite;

use App\Domain\Common\Type\Id;

interface VisiteRepository
{
    public function find(Id $audit_id): ?Visite;
}
