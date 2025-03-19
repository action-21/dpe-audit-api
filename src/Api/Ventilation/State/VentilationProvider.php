<?php

namespace App\Api\Ventilation\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Api\Ventilation\GetVentilationHandler;
use App\Api\Ventilation\Resource\VentilationResource;
use App\Domain\Common\ValueObject\Id;

/**
 * @implements ProviderInterface<VentilationResource|null>
 */
final class VentilationProvider implements ProviderInterface
{
    public function __construct(private GetVentilationHandler $handler) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): ?VentilationResource
    {
        $id = $uriVariables['id'] ? Id::from($uriVariables['id']) : null;
        $entity = $id ? ($this->handler)($id) : null;
        dump($entity->installations());
        return $entity ? VentilationResource::from($entity) : null;
    }
}
