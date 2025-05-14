<?php

namespace App\Api\Audit\Model;

use App\Domain\Audit\Audit as Entity;
use App\Domain\Audit\Enum\TypeBatiment;
use App\Services\Validator\Constraints as DpeAssert;
use Symfony\Component\Validator\Constraints as Assert;

final class Batiment
{
    public TypeBatiment $type;

    public function __construct(
        #[DpeAssert\Annee]
        public int $annee_construction,

        public float $altitude,

        #[Assert\Positive]
        public int $logements,

        #[Assert\Positive]
        public float $surface_habitable,

        #[Assert\Positive]
        public float $hauteur_sous_plafond,

        public bool $materiaux_anciens,

        public ?string $rnb_id,
    ) {}

    public static function from(Entity $entity): self
    {
        $value = new self(
            annee_construction: $entity->batiment()->annee_construction->value(),
            altitude: $entity->batiment()->altitude,
            logements: $entity->batiment()->logements,
            surface_habitable: $entity->batiment()->surface_habitable,
            hauteur_sous_plafond: $entity->batiment()->hauteur_sous_plafond,
            materiaux_anciens: $entity->batiment()->materiaux_anciens,
            rnb_id: $entity->batiment()->rnb_id,
        );

        $value->type = $entity->batiment()->type;
        return $value;
    }
}
