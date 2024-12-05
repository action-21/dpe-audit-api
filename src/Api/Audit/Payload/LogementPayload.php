<?php

namespace App\Api\Audit\Payload;

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
}
