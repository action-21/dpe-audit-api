<?php

namespace App\Api\Simulation;

use App\Api\Simulation\Payload\SimulationTravauxPayload;
use App\Domain\Common\ValueObject\Id;
use App\Domain\Simulation\{Simulation, SimulationRepository, SimulationService};

final class SimulationHandler
{
    public function __construct(
        private SimulationRepository $repository,
        private SimulationService $service,
    ) {}

    public function __invoke(Id $id, ?SimulationTravauxPayload $payload = null,): ?Simulation
    {
        if (null === $simulation = $this->repository->find($id)) {
            return null;
        }

        return $this->service->calcule($simulation);
    }
}
