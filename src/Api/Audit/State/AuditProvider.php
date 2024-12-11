<?php

namespace App\Api\Audit\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Api\Audit\GetAuditHandler;
use App\Api\Audit\Resource\AuditResource;
use App\Domain\Common\Type\Id;

/**
 * @implements ProviderInterface<AuditResource|null>
 */
final class AuditProvider implements ProviderInterface
{
    public function __construct(private GetAuditHandler $handler) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): ?AuditResource
    {
        $id = $uriVariables['id'] ? Id::from($uriVariables['id']) : null;
        $entity = $id ? ($this->handler)($id) : null;
        return $entity ? AuditResource::from($entity) : null;
    }
}
