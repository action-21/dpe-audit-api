<?php

namespace App\Domain\Enveloppe\Data\Baie;

use Webmozart\Assert\Assert;

final class MasqueLointainData
{
    public function __construct(public readonly ?float $fe2, public readonly ?float $omb,) {}

    public static function create(?float $fe2 = null, ?float $omb = null,): self
    {
        Assert::nullOrGreaterThanEq($fe2, 0);
        Assert::nullOrGreaterThanEq($omb, 0);
        Assert::nullOrLessThanEq($omb, 100);
        return new self(fe2: $fe2, omb: $omb);
    }

    public function with(
        ?float $fe2 = null,
        ?float $omb = null,
    ): self {
        return self::create(
            fe2: $fe2 ?? $this->fe2,
            omb: $omb ?? $this->omb,
        );
    }
}
