<?php

namespace App\Api\Visite\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Api\Visite\GetVisiteHandler;
use App\Api\Visite\Resource\VisiteResource;
use App\Domain\Common\Type\Id;

/**
 * @implements ProviderInterface<VisiteResource|null>
 */
final class VisiteProvider implements ProviderInterface
{
    public function __construct(private GetVisiteHandler $handler) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): ?VisiteResource
    {
        $id = $uriVariables['id'] ? Id::from($uriVariables['id']) : null;
        $entity = $id ? ($this->handler)($id) : null;
        return $entity ? VisiteResource::from($entity) : null;
    }
}
