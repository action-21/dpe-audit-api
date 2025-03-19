<?php

namespace App\Api\Ventilation\Resource;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Common\ValueObject\Consommation;
use App\Domain\Ventilation\Ventilation as Entity;

/**
 * @property GenerateurResource[] $generateurs
 * @property InstallationResource[] $installations
 * @property Consommation[] $consommations
 */
final class VentilationResource
{
    public function __construct(
        public readonly Id $audit_id,
        /** @var GenerateurResource[] */
        public readonly array $generateurs,
        /** @var InstallationResource[] */
        public readonly array $installations,
        /** @var Consommation[] */
        public readonly array $consommations,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
            audit_id: $entity->audit()->id(),
            generateurs: GenerateurResource::from_collection($entity->generateurs()),
            installations: InstallationResource::from_collection($entity->installations()),
            consommations: $entity->installations()->consommations()?->values() ?? [],
        );
    }
}
