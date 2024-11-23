<?php

namespace App\Domain\Audit\ValueObject;

use App\Domain\Common\Service\Assert;

/**
 * Caractéristiques du logement dans le cas d'un audit au périmètre du logement
 */
final class Logement
{
    public function __construct(
        public readonly string $description,
        public readonly float $surface_habitable,
        public readonly float $hauteur_sous_plafond,
    ) {}

    public static function create(
        string $description,
        float $surface_habitable,
        float $hauteur_sous_plafond,
    ): self {
        return new self(
            description: $description,
            surface_habitable: $surface_habitable,
            hauteur_sous_plafond: $hauteur_sous_plafond,
        );
    }

    public function controle(): void
    {
        Assert::positif($this->surface_habitable);
        Assert::positif($this->hauteur_sous_plafond);
    }
}
