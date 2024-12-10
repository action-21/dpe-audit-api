<?php

namespace App\Domain\PlancherHaut\ValueObject;

use App\Domain\PlancherHaut\Enum\{Inertie, TypePlancherHaut};
use Webmozart\Assert\Assert;

final class Caracteristique
{
    public function __construct(
        public readonly TypePlancherHaut $type,
        public readonly Inertie $inertie,
        public readonly float $surface,
        public readonly ?int $annee_construction,
        public readonly ?int $annee_renovation,
        public readonly ?float $u0,
        public readonly ?float $u,
    ) {}

    public static function create(
        TypePlancherHaut $type,
        Inertie $inertie,
        float $surface,
        ?int $annee_construction,
        ?int $annee_renovation,
        ?float $u0,
        ?float $u,
    ): self {
        Assert::greaterThan($surface, 0);
        Assert::nullOrGreaterThan($u0, 0);
        Assert::nullOrGreaterThan($u, 0);
        Assert::nullOrLessThanEq($annee_construction, (int) \date('Y'));
        Assert::nullOrLessThanEq($annee_renovation, (int) \date('Y'));

        return new self(
            type: $type,
            inertie: $inertie,
            surface: $surface,
            annee_construction: $annee_construction,
            annee_renovation: $annee_renovation,
            u0: $u0,
            u: $u,
        );
    }
}
