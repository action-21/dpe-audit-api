<?php

namespace App\Api\Audit\Payload;

use App\Domain\Audit\Enum\TypeBatiment;
use App\Domain\Audit\ValueObject\Batiment;
use Symfony\Component\Validator\Constraints as Assert;

final class BatimentPayload
{
    public function __construct(
        public TypeBatiment $type,
        public int $annee_construction,
        public int $altitude,
        #[Assert\Positive]
        public int $logements,
        #[Assert\Positive]
        public float $surface_habitable,
        #[Assert\Positive]
        public float $hauteur_sous_plafond,
    ) {}

    public function to(): Batiment
    {
        return Batiment::create(
            type: $this->type,
            annee_construction: $this->annee_construction,
            altitude: $this->altitude,
            logements: $this->logements,
            surface_habitable: $this->surface_habitable,
            hauteur_sous_plafond: $this->hauteur_sous_plafond,
        );
    }
}
