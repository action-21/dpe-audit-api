<?php

namespace App\Api\Ventilation;

use App\Domain\Ventilation\{Ventilation, VentilationRepository};
use App\Domain\Common\ValueObject\Id;

final class GetVentilationHandler
{
    public function __construct(private readonly VentilationRepository $repository) {}

    public function __invoke(Id $id): ?Ventilation
    {
        return $this->repository->find($id);
    }
}
