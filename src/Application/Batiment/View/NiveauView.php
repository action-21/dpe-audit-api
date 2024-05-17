<?php

namespace App\Application\Batiment\View;

use App\Domain\Batiment\Entity\{Niveau, NiveauCollection};

class NiveauView
{
    public function __construct(
        public readonly float $surface_habitable,
        public readonly float $hauteur_sous_plafond,
    ) {
    }

    public static function from_entity(Niveau $entity): self
    {
        return new self(
            surface_habitable: $entity->surface_habitable()->valeur(),
            hauteur_sous_plafond: $entity->hauteur_sous_plafond()->valeur(),
        );
    }

    /** @return self[] */
    public static function from_entity_collection(NiveauCollection $collection): array
    {
        return \array_map(fn (Niveau $entity) => self::from_entity($entity), $collection->to_array());
    }
}
