<?php

namespace App\Domain\PontThermique\ValueObject;

use App\Domain\Common\Service\Assert;

final class Performance
{
    public function __construct(
        public readonly float $kpt,
        public readonly float $pt,
    ) {}

    public static function create(float $kpt, float $pt): self
    {
        Assert::positif_ou_zero($kpt);
        Assert::positif_ou_zero($pt);
        return new self(kpt: $kpt, pt: $pt);
    }
}
