<?php

namespace App\Api\Ventilation;

use App\Domain\Ventilation\Ventilation;
use App\Domain\Ventilation\VentilationRepository;
use App\Domain\Common\ValueObject\Id;

final class GetVentilationHandler
{
    public function __construct(private VentilationRepository $repository) {}

    public function __invoke(Id $id): ?Ventilation
    {
        return $this->repository->find($id);
    }
}
