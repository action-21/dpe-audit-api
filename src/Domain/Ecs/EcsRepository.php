<?php

namespace App\Domain\Ecs;

use App\Domain\Common\Type\Id;

interface EcsRepository
{
    public function find(Id $audit_id): ?Ecs;
}
