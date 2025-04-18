<?php

namespace App\Api\Audit\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Api\Audit\GetAuditHandler;
use App\Api\Audit\Model\Audit;
use App\Domain\Common\ValueObject\Id;

/**
 * @implements ProviderInterface<Audit|null>
 */
final class AuditProvider implements ProviderInterface
{
    public function __construct(private GetAuditHandler $handler) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): ?Audit
    {
        $id = $uriVariables['id'] ? Id::from($uriVariables['id']) : null;
        $entity = $id ? ($this->handler)($id) : null;
        return $entity ? Audit::from($entity) : null;
    }
}
