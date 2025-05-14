<?php

namespace App\Api\Enveloppe\Model;

use App\Domain\Enveloppe\Entity\{Niveau as Entity, NiveauCollection as EntityCollection};
use App\Domain\Enveloppe\Enum\Inertie;
use Symfony\Component\Validator\Constraints as Assert;

final class Niveau
{
    public function __construct(
        #[Assert\Positive]
        public float $surface,

        public ?Inertie $inertie_paroi_verticale,

        public ?Inertie $inertie_plancher_bas,

        public ?Inertie $inertie_plancher_haut,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
            surface: $entity->surface(),
            inertie_paroi_verticale: $entity->inertie_paroi_verticale(),
            inertie_plancher_bas: $entity->inertie_plancher_bas(),
            inertie_plancher_haut: $entity->inertie_plancher_haut(),
        );
    }

    public static function from_collection(EntityCollection $collection): array
    {
        return $collection->map(fn(Entity $entity) => self::from($entity))->to_array();
    }
}
