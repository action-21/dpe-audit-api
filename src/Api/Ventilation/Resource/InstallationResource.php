<?php

namespace App\Api\Ventilation\Resource;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Common\ValueObject\Consommation;
use App\Domain\Ventilation\Entity\{Installation as Entity, InstallationCollection as EntityCollection};

/**
 * @property SystemeResource[] $systemes
 * @property Consommation[] $consommations
 */
final class InstallationResource
{
    public function __construct(
        public readonly Id $id,
        public readonly float $surface,
        /** @var SystemeResource[] */
        public readonly array $systemes,
        /** @var Consommation[] */
        public readonly array $consommations,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
            id: $entity->id(),
            surface: $entity->surface(),
            systemes: SystemeResource::from_collection($entity->systemes()),
            consommations: $entity->systemes()->consommations()?->values() ?? [],
        );
    }

    /** @return self[] */
    public static function from_collection(EntityCollection $collection): array
    {
        return $collection->map(fn(Entity $item): self => self::from($item))->values();
    }
}
