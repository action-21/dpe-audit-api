<?php

namespace App\Domain\Audit\ValueObject;

use App\Domain\Common\Service\Assert;

final class Occupation
{
    public function __construct(
        public readonly float $nadeq,
        public readonly float $nmax,
    ) {}

    public static function create(float $nadeq, float $nmax): self
    {
        Assert::positif_ou_zero($nadeq);
        Assert::positif_ou_zero($nmax);
        return new self(nadeq: $nadeq, nmax: $nmax,);
    }
}
