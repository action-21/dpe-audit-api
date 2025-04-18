<?php

namespace App\Api\Chauffage;

use App\Domain\Chauffage\{Chauffage, ChauffageRepository};
use App\Domain\Common\ValueObject\Id;

final class GetChauffageHandler
{
    public function __construct(private ChauffageRepository $repository) {}

    public function __invoke(Id $id): ?Chauffage
    {
        return $this->repository->find($id);
    }
}
