<?php

namespace App\Application\Logement\View;

use App\Domain\Logement\Entity\{Etage, EtageCollection};

class EtageView
{
    public function __construct(
        public readonly string $description,
        public readonly float $surface_habitable,
        public readonly float $hauteur_sous_plafond,
    ) {
    }

    public static function from_entity(Etage $entity): self
    {
        return new self(
            description: $entity->description(),
            surface_habitable: $entity->surface_habitable()->valeur(),
            hauteur_sous_plafond: $entity->hauteur_sous_plafond()->valeur(),
        );
    }

    /** @return self[] */
    public static function from_entity_collection(EtageCollection $collection): array
    {
        return \array_map(fn (Etage $entity) => self::from_entity($entity), $collection->to_array());
    }
}
