<?php

namespace App\Domain\Audit\ValueObject;

use Webmozart\Assert\Assert;

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
        Assert::greaterThan($surface_habitable, 0);
        Assert::greaterThan($hauteur_sous_plafond, 0);

        return new self(
            description: $description,
            surface_habitable: $surface_habitable,
            hauteur_sous_plafond: $hauteur_sous_plafond,
        );
    }
}
