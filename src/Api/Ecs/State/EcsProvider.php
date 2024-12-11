<?php

namespace App\Api\Ecs\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Api\Ecs\GetEcsHandler;
use App\Api\Ecs\Resource\EcsResource;
use App\Domain\Common\Type\Id;

/**
 * @implements ProviderInterface<EcsResource|null>
 */
final class EcsProvider implements ProviderInterface
{
    public function __construct(private GetEcsHandler $handler) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): ?EcsResource
    {
        $id = $uriVariables['id'] ? Id::from($uriVariables['id']) : null;
        $entity = $id ? ($this->handler)($id) : null;
        return $entity ? EcsResource::from($entity) : null;
    }
}
