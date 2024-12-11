<?php

namespace App\Api\Ecs;

use App\Domain\Ecs\Ecs;
use App\Domain\Ecs\EcsRepository;
use App\Domain\Common\Type\Id;

final class GetEcsHandler
{
    public function __construct(private EcsRepository $repository) {}

    public function __invoke(Id $id): ?Ecs
    {
        return $this->repository->find($id);
    }
}
