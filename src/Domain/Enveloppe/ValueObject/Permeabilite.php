<?php

namespace App\Domain\Enveloppe\ValueObject;

use App\Domain\Common\Service\Assert;

final class Permeabilite
{
    public function __construct(
        public readonly float $hvent,
        public readonly float $hperm,
        public readonly float $q4pa_conv,
    ) {}

    public static function create(float $hvent, float $hperm, float $q4pa_conv): self
    {
        Assert::positif_ou_zero($hvent);
        Assert::positif_ou_zero($hperm);
        Assert::positif_ou_zero($q4pa_conv);

        return new self(
            hvent: $hvent,
            hperm: $hperm,
            q4pa_conv: $q4pa_conv,
        );
    }
}
