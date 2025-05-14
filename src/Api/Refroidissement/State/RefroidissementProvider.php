<?php

namespace App\Api\Refroidissement\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Api\Refroidissement\GetRefroidissementHandler;
use App\Api\Refroidissement\Model\Refroidissement;
use App\Domain\Common\ValueObject\Id;

/**
 * @implements ProviderInterface<Refroidissement|null>
 */
final class RefroidissementProvider implements ProviderInterface
{
    public function __construct(private readonly GetRefroidissementHandler $handler) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): ?Refroidissement
    {
        $id = $uriVariables['id'] ? Id::from($uriVariables['id']) : null;
        $entity = $id ? ($this->handler)($id) : null;
        return $entity ? Refroidissement::from($entity) : null;
    }
}
