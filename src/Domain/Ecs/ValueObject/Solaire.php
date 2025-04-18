<?php

namespace App\Domain\Ecs\ValueObject;

use App\Domain\Common\ValueObject\{Annee, Pourcentage};
use App\Domain\Ecs\Enum\UsageEcs;
use Webmozart\Assert\Assert;

final class Solaire
{
    public function __construct(
        public readonly UsageEcs $usage,
        public readonly ?Annee $annee_installation,
        public readonly ?Pourcentage $fecs,
    ) {}

    public static function create(
        UsageEcs $usage,
        ?Annee $annee_installation,
        ?Pourcentage $fecs,
    ): self {
        Assert::nullOrGreaterThanEq($fecs?->value(), 0);
        Assert::nullOrLessThanEq($fecs?->value(), 100);

        return new self(
            usage: $usage,
            annee_installation: $annee_installation,
            fecs: $fecs,
        );
    }
}
