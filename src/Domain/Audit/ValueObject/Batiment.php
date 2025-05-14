<?php

namespace App\Domain\Audit\ValueObject;

use App\Domain\Audit\Enum\TypeBatiment;
use App\Domain\Common\ValueObject\{Annee, Id};
use Webmozart\Assert\Assert;

final class Batiment
{
    public function __construct(
        public readonly TypeBatiment $type,
        public readonly Annee $annee_construction,
        public readonly float $altitude,
        public readonly int $logements,
        public readonly float $surface_habitable,
        public readonly float $hauteur_sous_plafond,
        public readonly float $volume_habitable,
        public readonly bool $materiaux_anciens,
        public readonly ?Id $rnb_id,
    ) {}

    public static function create(
        TypeBatiment $type,
        Annee $annee_construction,
        float $altitude,
        int $logements,
        float $surface_habitable,
        float $hauteur_sous_plafond,
        bool $materiaux_anciens,
        ?Id $rnb_id,
    ): self {
        Assert::greaterThan($logements, 0);
        Assert::greaterThan($surface_habitable, 0);
        Assert::greaterThan($hauteur_sous_plafond, 0);

        return new self(
            type: $type,
            altitude: $altitude,
            annee_construction: $annee_construction,
            logements: $logements,
            surface_habitable: $surface_habitable,
            hauteur_sous_plafond: $hauteur_sous_plafond,
            volume_habitable: $surface_habitable * $hauteur_sous_plafond,
            materiaux_anciens: $materiaux_anciens,
            rnb_id: $rnb_id,
        );
    }
}
