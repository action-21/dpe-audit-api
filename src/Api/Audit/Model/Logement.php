<?php

namespace App\Api\Audit\Model;

use App\Domain\Audit\Entity\{Logement as Entity, LogementCollection as EntityCollection};
use App\Domain\Audit\Enum\{PositionLogement, Typologie};
use Symfony\Component\Validator\Constraints as Assert;

final class Logement
{
    public function __construct(
        public string $id,
        public string $description,
        public PositionLogement $position,
        public Typologie $typologie,
        #[Assert\GreaterThan(0)]
        public float $surface_habitable,
        #[Assert\GreaterThan(0)]
        public float $hauteur_sous_plafond,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
            id: $entity->id(),
            description: $entity->description(),
            position: $entity->position(),
            typologie: $entity->typologie(),
            surface_habitable: $entity->surface_habitable(),
            hauteur_sous_plafond: $entity->hauteur_sous_plafond(),
        );
    }

    public static function from_collection(EntityCollection $collection): array
    {
        return $collection->map(fn(Entity $entity) => self::from($entity))->values();
    }
}
