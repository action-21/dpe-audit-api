<?php

namespace App\Api\Eclairage;

use App\Domain\Eclairage\{Eclairage, EclairageRepository};
use App\Domain\Common\ValueObject\Id;

final class GetEclairageHandler
{
    public function __construct(private EclairageRepository $repository) {}

    public function __invoke(Id $id): ?Eclairage
    {
        return $this->repository->find($id);
    }
}
