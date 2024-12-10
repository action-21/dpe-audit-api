<?php

namespace App\Domain\Chauffage\ValueObject;

use Webmozart\Assert\Assert;

final class Solaire
{
    public function __construct(public readonly ?float $fch,) {}

    public static function create(?float $fch): self
    {
        Assert::nullOrGreaterThanEq($fch, 0);
        Assert::nullOrLessThanEq($fch, 1);
        return new self(fch: $fch,);
    }
}
