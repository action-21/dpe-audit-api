<?php

namespace App\Api\Production\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Api\Production\GetProductionHandler;
use App\Api\Production\Resource\ProductionResource;
use App\Domain\Common\Type\Id;

/**
 * @implements ProviderInterface<ProductionResource|null>
 */
final class ProductionProvider implements ProviderInterface
{
    public function __construct(private GetProductionHandler $handler) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): ?ProductionResource
    {
        $id = $uriVariables['id'] ? Id::from($uriVariables['id']) : null;
        $entity = $id ? ($this->handler)($id) : null;
        return $entity ? ProductionResource::from($entity) : null;
    }
}
