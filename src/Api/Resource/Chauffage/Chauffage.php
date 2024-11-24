<?php

namespace App\Api\Resource\Chauffage;

use App\Domain\Common\Type\Id;
use App\Domain\Common\ValueObject\{Besoin, Consommation};
use App\Domain\Chauffage\Chauffage as Entity;
use ApiPlatform\Metadata\{ApiProperty, ApiResource};

final class Chauffage
{
    public function __construct(
        #[ApiProperty(identifier: true, readable: false, writable: false)]
        public readonly Id $audit_id,
        /** @var Generateur[] */
        public readonly array $generateurs,
        /** @var Emetteur[] */
        public readonly array $emetteurs,
        /** @var Installation[] */
        public readonly array $installations,
        /** @var Besoin[] */
        public readonly array $besoins_bruts,
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
            emetteurs: Emetteur::from_collection($entity->emetteurs()),
            installations: Installation::from_collection($entity->installations()),
            besoins_bruts: $entity->besoins_bruts()?->values() ?? [],
            besoins: $entity->besoins()?->values() ?? [],
            consommations: $entity->installations()->consommations()?->values() ?? [],
        );
    }
}
