<?php

namespace App\Domain\Enveloppe\ValueObject;

use App\Domain\Enveloppe\Enum\TypeDeperdition;
use Webmozart\Assert\Assert;

final class Deperdition
{
    public function __construct(
        public readonly TypeDeperdition $type,
        public readonly float $deperdition,
    ) {}

    public static function create(TypeDeperdition $type, float $deperdition,): self
    {
        Assert::greaterThanEq($deperdition, 0);

        return new self(
            type: $type,
            deperdition: $deperdition,
        );
    }
}
