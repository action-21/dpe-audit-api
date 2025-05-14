<?php

namespace App\Api\Production;

use App\Domain\Production\{Production, ProductionRepository};
use App\Domain\Common\ValueObject\Id;

final class GetProductionHandler
{
    public function __construct(private readonly ProductionRepository $repository) {}

    public function __invoke(Id $id): ?Production
    {
        return $this->repository->find($id);
    }
}
