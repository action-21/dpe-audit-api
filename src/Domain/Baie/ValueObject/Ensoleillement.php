<?php

namespace App\Domain\Baie\ValueObject;

use App\Domain\Common\Enum\Mois;
use Webmozart\Assert\Assert;

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
        Assert::greaterThanEq($fe, 0);
        Assert::greaterThanEq($sw, 0);
        Assert::greaterThanEq($c1, 0);
        Assert::greaterThanEq($sse, 0);

        return new self($mois, $fe, $sw, $c1, $sse);
    }
}
