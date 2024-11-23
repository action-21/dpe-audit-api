<?php

namespace App\Domain\Baie\ValueObject;

use App\Domain\Common\Enum\Mois;
use App\Domain\Common\Service\Assert;

final class Ensoleillement
{
    public function __construct(
        public readonly Mois $mois,
        public readonly float $fe,
        public readonly float $sw,
        public readonly float $c1,
        public readonly float $sse,
    ) {}

    public static function create(Mois $mois, float $fe, float $sw, float $c1, float $sse): self
    {
        Assert::positif_ou_zero($fe);
        Assert::positif_ou_zero($sw);
        Assert::positif_ou_zero($c1);
        Assert::positif_ou_zero($sse);

        return new self($mois, $fe, $sw, $c1, $sse);
    }
}
