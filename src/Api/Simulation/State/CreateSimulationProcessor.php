<?php

namespace App\Api\Simulation\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Api\Simulation\CreateSimulationHandler;
use App\Api\Simulation\Payload\SimulationPayload;
use App\Api\Simulation\Resource\SimulationResource;

/**
 * @implements ProcessorInterface<SimulationPayload, SimulationResource>
 */
final class CreateSimulationProcessor implements ProcessorInterface
{
    public function __construct(private CreateSimulationHandler $handler,) {}

    /**
     * @param SimulationPayload $data
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): SimulationResource
    {
        $simulation = ($this->handler)($data);
        return SimulationResource::from($simulation);
    }
}
