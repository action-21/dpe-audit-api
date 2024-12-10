<?php

namespace App\Domain\Production\ValueObject;

use App\Domain\Common\Enum\Mois;
use Webmozart\Assert\Assert;

final class ProductionPhotovoltaique
{
    public function __construct(
        public readonly Mois $mois,
        public readonly float $ppv,
    ) {}

    public static function create(Mois $mois, float $ppv): self
    {
        Assert::greaterThanEq($ppv, 0);
        return new self(mois: $mois, ppv: $ppv);
    }
}
