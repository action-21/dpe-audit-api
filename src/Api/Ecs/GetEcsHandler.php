<?php

namespace App\Api\Ecs;

use App\Domain\Ecs\{Ecs, EcsRepository};
use App\Domain\Common\ValueObject\Id;

final class GetEcsHandler
{
    public function __construct(private readonly EcsRepository $repository) {}

    public function __invoke(Id $id): ?Ecs
    {
        return $this->repository->find($id);
    }
}
