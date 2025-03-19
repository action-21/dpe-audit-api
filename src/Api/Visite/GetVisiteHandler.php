<?php

namespace App\Api\Visite;

use App\Domain\Visite\Visite;
use App\Domain\Visite\VisiteRepository;
use App\Domain\Common\ValueObject\Id;

final class GetVisiteHandler
{
    public function __construct(private VisiteRepository $repository) {}

    public function __invoke(Id $id): ?Visite
    {
        return $this->repository->find($id);
    }
}
