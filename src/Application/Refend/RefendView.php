<?php

namespace App\Application\Refend;

use App\Domain\Refend\{Refend, RefendCollection};

class RefendView
{
    public function __construct(
        public readonly string $id,
        public readonly string $description,
        public readonly bool $refend_lourd,
        public readonly float $lineaire,
        public readonly float $epaisseur,
        public readonly float $emprise,
    ) {
    }

    public static function from_entity(Refend $entity): self
    {
        return new self(
            id: $entity->id(),
            description: $entity->description(),
            refend_lourd: $entity->refend_lourd(),
            lineaire: $entity->dimensions()->lineaire->valeur(),
            epaisseur: $entity->dimensions()->epaisseur->valeur(),
            emprise: $entity->dimensions()->emprise(),
        );
    }

    /** @return self[] */
    public static function from_entity_collection(RefendCollection $collection): array
    {
        return \array_map(fn (Refend $entity) => self::from_entity($entity), $collection->to_array());
    }
}
