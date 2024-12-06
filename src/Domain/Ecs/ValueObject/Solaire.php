<?php

namespace App\Domain\Ecs\ValueObject;

use App\Domain\Ecs\Enum\UsageEcs;
use Webmozart\Assert\Assert;

final class Solaire
{
    public function __construct(
        public readonly UsageEcs $usage,
        public readonly ?int $annee_installation,
        public readonly ?float $fecs,
    ) {}

    public static function create(
        UsageEcs $usage,
        ?int $annee_installation,
        ?float $fecs,
    ): self {
        Assert::nullOrGreaterThanEq($fecs, 0);
        Assert::nullOrLessThanEq($fecs, 1);
        Assert::nullOrLessThanEq($annee_installation, (int) date('Y'));

        return new self(
            usage: $usage,
            annee_installation: $annee_installation,
            fecs: $fecs,
        );
    }
}
