<?php

namespace App\Application\PlancherIntermediaire;

use App\Domain\PlancherIntermediaire\{PlancherIntermediaire, PlancherIntermediaireCollection};

class PlancherIntermediaireView
{
    public function __construct(
        public readonly string $id,
        public readonly string $description,
        public readonly bool $plancher_haut_lourd,
        public readonly bool $plancher_bas_lourd,
        public readonly float $surface,
        public readonly float $lineaire,
        public readonly float $epaisseur,
        public readonly float $emprise,
    ) {
    }

    public static function from_entity(PlancherIntermediaire $entity): self
    {
        return new self(
            id: $entity->id(),
            description: $entity->description(),
            plancher_haut_lourd: $entity->plancher_haut_lourd(),
            plancher_bas_lourd: $entity->plancher_bas_lourd(),
            lineaire: $entity->dimensions()->lineaire->valeur(),
            epaisseur: $entity->dimensions()->epaisseur->valeur(),
            surface: $entity->dimensions()->surface->valeur(),
            emprise: $entity->dimensions()->emprise(),
        );
    }

    /** @return self[] */
    public static function from_entity_collection(PlancherIntermediaireCollection $collection): array
    {
        return \array_map(fn (PlancherIntermediaire $entity) => self::from_entity($entity), $collection->to_array());
    }
}
