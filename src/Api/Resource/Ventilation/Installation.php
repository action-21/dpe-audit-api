<?php

namespace App\Api\Resource\Ventilation;

use App\Domain\Common\Type\Id;
use App\Domain\Common\ValueObject\Consommation;
use App\Domain\Ventilation\Entity\{Installation as Entity, InstallationCollection as EntityCollection};
use ApiPlatform\Metadata\{ApiProperty, ApiResource};

final class Installation
{
    public function __construct(
        #[ApiProperty(identifier: true, readable: false, writable: false)]
        public readonly Id $id,
        public readonly float $surface,
        /** @var Systeme[] */
        public readonly array $systemes,
        /** @var Consommation[] */
        public readonly array $consommations,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
            id: $entity->id(),
            surface: $entity->surface(),
            systemes: Systeme::from_collection($entity->systemes()),
            consommations: $entity->systemes()->consommations()?->values() ?? [],
        );
    }

    /** @return self[] */
    public static function from_collection(EntityCollection $collection): array
    {
        return $collection->map(fn(Entity $item): self => self::from($item))->values();
    }
}
