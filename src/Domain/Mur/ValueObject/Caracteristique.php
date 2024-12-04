<?php

namespace App\Domain\Mur\ValueObject;

use App\Domain\Mur\Enum\{Inertie, TypeDoublage, TypeMur};
use Webmozart\Assert\Assert;

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
        Assert::greaterThan($surface, 0);
        Assert::greaterThan($epaisseur, 0);
        Assert::greaterThan($u0, 0);
        Assert::greaterThan($u, 0);
        Assert::lessThanEq($annee_construction, (int) \date('Y'));
        Assert::lessThanEq($annee_renovation, (int) \date('Y'));

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

    public function epaisseur_defaut(): float
    {
        return $this->epaisseur ?? $this->type->epaisseur_defaut();
    }
}
