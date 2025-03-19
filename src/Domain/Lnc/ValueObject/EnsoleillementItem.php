<?php

namespace App\Domain\Lnc\ValueObject;

use App\Domain\Common\Enum\Mois;
use Webmozart\Assert\Assert;

final class EnsoleillementItem
{
    public function __construct(
        public readonly Mois $mois,
        public readonly float $t,
        public readonly float $sst,
    ) {}

    public static function create(Mois $mois, float $t, float $sst,): self
    {
        Assert::greaterThanEq($t, 0);
        Assert::greaterThanEq($sst, 0);

        return new self($mois, $t, $sst);
    }
}
