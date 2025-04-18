<?php

namespace App\Api\Production\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Api\Production\GetProductionHandler;
use App\Api\Production\Model\Production;
use App\Domain\Common\ValueObject\Id;

/**
 * @implements ProviderInterface<Production|null>
 */
final class ProductionProvider implements ProviderInterface
{
    public function __construct(private GetProductionHandler $handler) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): ?Production
    {
        $id = $uriVariables['id'] ? Id::from($uriVariables['id']) : null;
        $entity = $id ? ($this->handler)($id) : null;
        return $entity ? Production::from($entity) : null;
    }
}
