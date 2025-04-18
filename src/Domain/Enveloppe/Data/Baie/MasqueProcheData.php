<?php

namespace App\Domain\Enveloppe\Data\Baie;

use Webmozart\Assert\Assert;

final class MasqueProcheData
{
    public function __construct(public readonly ?float $fe1) {}

    public static function create(?float $fe1 = null): self
    {
        Assert::nullOrGreaterThanEq($fe1, 0);
        return new self(fe1: $fe1);
    }

    public function with(?float $fe1 = null): self
    {
        return self::create(
            fe1: $fe1 ?? $this->fe1,
        );
    }
}
