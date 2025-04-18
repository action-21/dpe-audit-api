<?php

namespace App\Api\Audit\Model;

use App\Domain\Audit\Audit as Entity;
use App\Services\Validator\Constraints as DpeAssert;
use Symfony\Component\Validator\Constraints as Assert;

final class Batiment
{
    public function __construct(
        #[DpeAssert\Annee]
        public readonly int $annee_construction,

        public readonly float $altitude,

        #[Assert\Positive]
        public readonly int $logements,

        #[Assert\Positive]
        public readonly float $surface_habitable,

        #[Assert\Positive]
        public readonly float $hauteur_sous_plafond,

        public readonly bool $materiaux_anciens,

        public readonly ?string $rnb_id,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
            annee_construction: $entity->batiment()->annee_construction->value(),
            altitude: $entity->batiment()->altitude,
            logements: $entity->batiment()->logements,
            surface_habitable: $entity->batiment()->surface_habitable,
            hauteur_sous_plafond: $entity->batiment()->hauteur_sous_plafond,
            materiaux_anciens: $entity->batiment()->materiaux_anciens,
            rnb_id: $entity->batiment()->rnb_id,
        );
    }
}
