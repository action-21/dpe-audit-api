<?php

namespace App\Domain\Lnc\ValueObject;

use App\Domain\Common\Enum\Mois;
use App\Domain\Common\Service\Assert;

final class Ensoleillement
{
    public function __construct(
        public readonly Mois $mois,
        public readonly float $t,
        public readonly float $sst,
    ) {}

    public static function create(Mois $mois, float $t, float $sst,): self
    {
        Assert::positif_ou_zero($t);
        Assert::positif_ou_zero($sst);

        return new self($mois, $t, $sst);
    }
}
