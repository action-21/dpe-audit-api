<?php

namespace App\Domain\Audit\ValueObject;

use Webmozart\Assert\Assert;

final class Occupation
{
    public function __construct(
        public readonly float $nadeq,
        public readonly float $nmax,
    ) {}

    public static function create(float $nadeq, float $nmax): self
    {
        $value = new self(nadeq: $nadeq, nmax: $nmax,);
        $value->controle();
        return $value;
    }

    public function controle(): void
    {
        Assert::greaterThanEq($this->nadeq, 0);
        Assert::greaterThanEq($this->nmax, 0);
    }
}
