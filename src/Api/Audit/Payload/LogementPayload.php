<?php

namespace App\Api\Audit\Payload;

use App\Domain\Audit\ValueObject\Logement;
use Symfony\Component\Validator\Constraints as Assert;

final class LogementPayload
{
    public function __construct(
        public string $description,
        #[Assert\Positive]
        public float $surface_habitable,
        #[Assert\Positive]
        public float $hauteur_sous_plafond,
    ) {}

    public function to(): Logement
    {
        return Logement::create(
            description: $this->description,
            surface_habitable: $this->surface_habitable,
            hauteur_sous_plafond: $this->hauteur_sous_plafond,
        );
    }
}
