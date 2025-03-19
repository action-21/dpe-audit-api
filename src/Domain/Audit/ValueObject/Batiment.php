<?php

namespace App\Domain\Audit\ValueObject;

use App\Domain\Audit\Enum\TypeBatiment;
use Webmozart\Assert\Assert;

final class Batiment
{
    public readonly TypeBatiment $type;
    public readonly float $volume_habitable;

    public function __construct(
        public readonly int $annee_construction,
        public readonly int $altitude,
        public readonly int $logements,
        public readonly float $surface_habitable,
        public readonly float $hauteur_sous_plafond,
    ) {}

    public static function create(
        int $annee_construction,
        int $altitude,
        int $logements,
        float $surface_habitable,
        float $hauteur_sous_plafond,
    ): self {
        $value = new self(
            altitude: $altitude,
            annee_construction: $annee_construction,
            logements: $logements,
            surface_habitable: $surface_habitable,
            hauteur_sous_plafond: $hauteur_sous_plafond,
        );

        $value->controle();
        $value->type = TypeBatiment::from_nombre_logements($logements);
        $value->volume_habitable = $surface_habitable * $hauteur_sous_plafond;
        return $value;
    }

    public function controle(): void
    {
        Assert::lessThanEq($this->annee_construction, (int) \date('Y'));
        Assert::greaterThan($this->logements, 0);
        Assert::greaterThan($this->surface_habitable, 0);
        Assert::greaterThan($this->hauteur_sous_plafond, 0);
    }

    public function surface_habitable_moyenne(): float
    {
        return $this->surface_habitable / $this->logements;
    }
}
