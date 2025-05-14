<?php

namespace App\Api\Refroidissement;

use App\Domain\Refroidissement\{Refroidissement, RefroidissementRepository};
use App\Domain\Common\ValueObject\Id;

final class GetRefroidissementHandler
{
    public function __construct(private readonly RefroidissementRepository $repository) {}

    public function __invoke(Id $id): ?Refroidissement
    {
        return $this->repository->find($id);
    }
}
