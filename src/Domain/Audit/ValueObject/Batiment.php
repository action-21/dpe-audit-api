<?php

namespace App\Domain\Audit\ValueObject;

use App\Domain\Audit\Enum\TypeBatiment;
use App\Domain\Common\ValueObject\Annee;
use App\Domain\Common\ValueObject\Id;
use Webmozart\Assert\Assert;

final class Batiment
{
    public function __construct(
        public readonly Annee $annee_construction,
        public readonly int $altitude,
        public readonly int $logements,
        public readonly float $surface_habitable,
        public readonly float $hauteur_sous_plafond,
        public readonly TypeBatiment $type,
        public readonly float $volume_habitable,
        public readonly bool $materiaux_anciens,
        public readonly ?Id $rnb_id,
    ) {}

    public static function create(
        Annee $annee_construction,
        int $altitude,
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
            altitude: $altitude,
            annee_construction: $annee_construction,
            logements: $logements,
            surface_habitable: $surface_habitable,
            hauteur_sous_plafond: $hauteur_sous_plafond,
            type: TypeBatiment::from_nombre_logements($logements),
            volume_habitable: $surface_habitable * $hauteur_sous_plafond,
            materiaux_anciens: $materiaux_anciens,
            rnb_id: $rnb_id,
        );
    }
}
