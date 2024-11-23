<?php

namespace App\Domain\Audit\ValueObject;

use App\Domain\Audit\Enum\TypeBatiment;
use App\Domain\Common\Service\Assert;

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
        int $annee_construction,
        int $altitude,
        int $logements,
        float $surface_habitable,
        float $hauteur_sous_plafond,
    ): self {
        return new self(
            altitude: $altitude,
            annee_construction: $annee_construction,
            logements: $logements,
            type: $logements <= 2 ? TypeBatiment::MAISON : TypeBatiment::IMMEUBLE,
            surface_habitable: $surface_habitable,
            hauteur_sous_plafond: $hauteur_sous_plafond,
            volume_habitable: $surface_habitable * $hauteur_sous_plafond,
        );
    }

    public function controle(): void
    {
        Assert::annee($this->annee_construction);
        Assert::positif($this->logements);
        Assert::positif($this->surface_habitable);
        Assert::positif($this->hauteur_sous_plafond);
        Assert::positif($this->volume_habitable);
    }
}
