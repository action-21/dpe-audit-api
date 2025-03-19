<?php

namespace App\Api\Enveloppe\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Api\Enveloppe\GetEnveloppeHandler;
use App\Api\Enveloppe\Resource\EnveloppeResource;
use App\Domain\Common\ValueObject\Id;

/**
 * @implements ProviderInterface<EnveloppeResource|null>
 */
final class EnveloppeProvider implements ProviderInterface
{
    public function __construct(private GetEnveloppeHandler $handler) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): ?EnveloppeResource
    {
        $id = $uriVariables['id'] ? Id::from($uriVariables['id']) : null;
        $entity = $id ? ($this->handler)($id) : null;
        return $entity ? EnveloppeResource::from($entity) : null;
    }
}
