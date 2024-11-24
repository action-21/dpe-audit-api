<?php

namespace App\Api\Resource\Refroidissement;

use App\Domain\Common\Type\Id;
use App\Domain\Common\ValueObject\{Besoin, Consommation};
use App\Domain\Refroidissement\Refroidissement as Entity;
use ApiPlatform\Metadata\{ApiProperty, ApiResource};

final class Refroidissement
{
    public function __construct(
        #[ApiProperty(identifier: true, readable: false, writable: false)]
        public readonly Id $audit_id,
        /** @var Generateur[] */
        public readonly array $generateurs,
        /** @var Installation[] */
        public readonly array $installations,
        /** @var Besoin[] */
        public readonly array $besoins,
        /** @var Consommation[] */
        public readonly array $consommations,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
            audit_id: $entity->audit()->id(),
            generateurs: Generateur::from_collection($entity->generateurs()),
            installations: Installation::from_collection($entity->installations()),
            besoins: $entity->besoins()?->values() ?? [],
            consommations: $entity->installations()->consommations()?->values() ?? [],
        );
    }
}
