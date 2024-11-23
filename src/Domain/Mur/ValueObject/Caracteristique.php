<?php

namespace App\Domain\Mur\ValueObject;

use App\Domain\Common\Service\Assert;
use App\Domain\Mur\Enum\{Inertie, TypeDoublage, TypeMur};
use App\Domain\Mur\Mur;

final class Caracteristique
{
    public function __construct(
        public readonly TypeMur $type,
        public readonly TypeDoublage $type_doublage,
        public readonly Inertie $inertie,
        public readonly float $surface,
        public readonly bool $presence_enduit_isolant,
        public readonly bool $paroi_ancienne,
        public readonly ?int $epaisseur,
        public readonly ?int $annee_construction,
        public readonly ?int $annee_renovation,
        public readonly ?float $u0,
        public readonly ?float $u,
    ) {}

    public static function create(
        TypeMur $type,
        TypeDoublage $type_doublage,
        Inertie $inertie,
        float $surface,
        bool $presence_enduit_isolant,
        bool $paroi_ancienne,
        ?int $epaisseur,
        ?int $annee_construction,
        ?int $annee_renovation,
        ?float $u0,
        ?float $u,
    ): self {
        return new self(
            type: $type,
            type_doublage: $type_doublage,
            inertie: $inertie,
            surface: $surface,
            presence_enduit_isolant: $presence_enduit_isolant,
            paroi_ancienne: $paroi_ancienne,
            epaisseur: $epaisseur,
            annee_construction: $annee_construction,
            annee_renovation: $annee_renovation,
            u0: $u0,
            u: $u,
        );
    }

    public function controle(Mur $entity): void
    {
        Assert::positif($this->surface);
        Assert::positif($this->epaisseur);
        Assert::annee($this->annee_construction);
        Assert::annee($this->annee_renovation);
        Assert::positif($this->u0);
        Assert::positif($this->u);
        Assert::superieur_ou_egal_a($this->annee_construction, $entity->enveloppe()->annee_construction_batiment());
        Assert::superieur_ou_egal_a($this->annee_renovation, $entity->enveloppe()->annee_construction_batiment());
    }

    public function epaisseur_defaut(): float
    {
        return $this->epaisseur ?? $this->type->epaisseur_defaut();
    }
}
