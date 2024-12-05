<?php

namespace App\Api\Audit\Payload;

use Symfony\Component\Validator\Constraints as Assert;

final class BatimentPayload
{
    public function __construct(
        public int $annee_construction,
        public int $altitude,
        #[Assert\Positive]
        public int $logements,
        #[Assert\Positive]
        public float $surface_habitable,
        #[Assert\Positive]
        public float $hauteur_sous_plafond,
    ) {}
}
