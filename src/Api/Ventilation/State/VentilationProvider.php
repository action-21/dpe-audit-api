<?php

namespace App\Api\Ventilation\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Api\Ventilation\GetVentilationHandler;
use App\Api\Ventilation\Model\Ventilation;
use App\Domain\Common\ValueObject\Id;

/**
 * @implements ProviderInterface<Ventilation|null>
 */
final class VentilationProvider implements ProviderInterface
{
    public function __construct(private readonly GetVentilationHandler $handler) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): ?Ventilation
    {
        $id = $uriVariables['id'] ? Id::from($uriVariables['id']) : null;
        $entity = $id ? ($this->handler)($id) : null;
        return $entity ? Ventilation::from($entity) : null;
    }
}
