<?php

namespace App\Api\Chauffage\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Api\Chauffage\GetChauffageHandler;
use App\Api\Chauffage\Resource\ChauffageResource;
use App\Domain\Common\Type\Id;

/**
 * @implements ProviderInterface<ChauffageResource|null>
 */
final class ChauffageProvider implements ProviderInterface
{
    public function __construct(private GetChauffageHandler $handler) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): ?ChauffageResource
    {
        $id = $uriVariables['id'] ? Id::from($uriVariables['id']) : null;
        $entity = $id ? ($this->handler)($id) : null;
        return $entity ? ChauffageResource::from($entity) : null;
    }
}
