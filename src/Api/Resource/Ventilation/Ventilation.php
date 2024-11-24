<?php

namespace App\Api\Resource\Ventilation;

use App\Domain\Common\Type\Id;
use App\Domain\Common\ValueObject\Consommation;
use App\Domain\Ventilation\Ventilation as Entity;
use ApiPlatform\Metadata\{ApiProperty, ApiResource};

final class Ventilation
{
    public function __construct(
        #[ApiProperty(identifier: true, readable: false, writable: false)]
        public readonly Id $audit_id,
        /** @var Generateur[] */
        public readonly array $generateurs,
        /** @var Installation[] */
        public readonly array $installations,
        /** @var Consommation[] */
        public readonly array $consommations,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
            audit_id: $entity->audit()->id(),
            generateurs: Generateur::from_collection($entity->generateurs()),
            installations: Installation::from_collection($entity->installations()),
            consommations: $entity->installations()->consommations()?->values() ?? [],
        );
    }
}
