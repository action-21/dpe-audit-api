<?php

namespace App\Domain\Ecs;

use App\Domain\Common\ValueObject\Id;

interface EcsRepository
{
    public function find(Id $audit_id): ?Ecs;
}
