<?php

namespace App\Api\Simulation;

use App\Api\Audit\CreateAuditHandler;
use App\Api\Simulation\Payload\SimulationPayload;
use App\Api\Ventilation\CreateVentilationHandler;
use App\Domain\Simulation\{Simulation, SimulationFactory};

final class CreateSimulationHandler
{
    public function __construct(
        private SimulationFactory $factory,
        private CreateAuditHandler $create_audit_handler,
        private CreateVentilationHandler $create_ventilation_handler,
    ) {}

    public function __invoke(SimulationPayload $payload): Simulation
    {
        $audit = ($this->create_audit_handler)($payload->audit);
        $ventilation = ($this->create_ventilation_handler)($payload->ventilation, $audit);

        $simulation = $this->factory->build(
            audit: $audit,
            ventilation: $ventilation,
        );

        return $simulation;
    }
}
