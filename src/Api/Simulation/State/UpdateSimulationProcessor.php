<?php

namespace App\Api\Simulation\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Api\Simulation\SimulationHandler;
use App\Api\Simulation\Payload\SimulationTravauxPayload;
use App\Api\Simulation\Resource\SimulationResource;
use App\Domain\Common\Type\Id;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @implements ProcessorInterface<SimulationTravauxPayload, SimulationResource>
 */
final class UpdateSimulationProcessor implements ProcessorInterface
{
    public function __construct(private SimulationHandler $handler,) {}

    /**
     * @param SimulationTravauxPayload $data
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): SimulationResource
    {
        if (null === $id = $uriVariables['id'] ? Id::from($uriVariables['id']) : null) {
            throw new NotFoundHttpException();
        }
        if (null === $simulation = ($this->handler)($id, $data)) {
            throw new NotFoundHttpException();
        }
        return SimulationResource::from($simulation);
    }
}
