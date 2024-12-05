<?php

namespace App\Domain\Audit\ValueObject;

use App\Domain\Audit\Enum\TypeBatiment;
use Webmozart\Assert\Assert;

final class Batiment
{
    public function __construct(
        public readonly TypeBatiment $type,
        public readonly int $annee_construction,
        public readonly int $altitude,
        public readonly int $logements,
        public readonly float $surface_habitable,
        public readonly float $hauteur_sous_plafond,
        public readonly float $volume_habitable,
    ) {}

    public static function create(
        TypeBatiment $type_batiment,
        int $annee_construction,
        int $altitude,
        int $logements,
        float $surface_habitable,
        float $hauteur_sous_plafond,
    ): self {
        Assert::lessThanEq($annee_construction, (int) \date('Y'));
        Assert::greaterThan($logements, 0);
        Assert::greaterThan($surface_habitable, 0);
        Assert::greaterThan($hauteur_sous_plafond, 0);

        return new self(
            altitude: $altitude,
            annee_construction: $annee_construction,
            logements: $logements,
            type: $logements > 2 ? TypeBatiment::IMMEUBLE : $type_batiment,
            surface_habitable: $surface_habitable,
            hauteur_sous_plafond: $hauteur_sous_plafond,
            volume_habitable: $surface_habitable * $hauteur_sous_plafond,
        );
    }
}
