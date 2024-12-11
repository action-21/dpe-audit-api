<?php

namespace App\Api\Refroidissement\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Api\Refroidissement\GetRefroidissementHandler;
use App\Api\Refroidissement\Resource\RefroidissementResource;
use App\Domain\Common\Type\Id;

/**
 * @implements ProviderInterface<RefroidissementResource|null>
 */
final class RefroidissementProvider implements ProviderInterface
{
    public function __construct(private GetRefroidissementHandler $handler) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): ?RefroidissementResource
    {
        $id = $uriVariables['id'] ? Id::from($uriVariables['id']) : null;
        $entity = $id ? ($this->handler)($id) : null;
        return $entity ? RefroidissementResource::from($entity) : null;
    }
}
