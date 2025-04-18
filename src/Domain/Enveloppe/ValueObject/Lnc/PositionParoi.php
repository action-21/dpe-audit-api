<?php

namespace App\Domain\Enveloppe\ValueObject\Lnc;

use App\Domain\Enveloppe\Enum\Mitoyennete;
use Webmozart\Assert\Assert;

final class PositionParoi
{
    public function __construct(
        public readonly Mitoyennete $mitoyennete,
        public readonly float $surface,
    ) {}

    public static function create(Mitoyennete $mitoyennete, float $surface,): self
    {
        Assert::greaterThan($surface, 0);
        return new self(mitoyennete: $mitoyennete, surface: $surface,);
    }
}
