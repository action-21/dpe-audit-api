<?php

namespace App\Api\Simulation\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Api\Simulation\SimulationHandler;
use App\Api\Simulation\Resource\SimulationResource;
use App\Domain\Common\Type\Id;

/**
 * @implements ProviderInterface<SimulationResource|null>
 */
final class GetSimulationProvider implements ProviderInterface
{
    public function __construct(private SimulationHandler $handler,) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): ?SimulationResource
    {
        if (null === $id = $uriVariables['id'] ? Id::from($uriVariables['id']) : null) {
            return null;
        }
        return ($entity = ($this->handler)($id)) ? SimulationResource::from($entity) : null;
    }
}
