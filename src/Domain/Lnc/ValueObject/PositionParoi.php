<?php

namespace App\Domain\Lnc\ValueObject;

use App\Domain\Lnc\Enum\Mitoyennete;
use Webmozart\Assert\Assert;

final class PositionParoi
{
    public function __construct(
        public readonly Mitoyennete $mitoyennete,
        public readonly float $surface,
    ) {}

    public static function create(Mitoyennete $mitoyennete, float $surface,): self
    {
        $value = new self(mitoyennete: $mitoyennete, surface: $surface,);
        $value->controle();
        return $value;
    }

    public function controle(): void
    {
        Assert::greaterThan($this->surface, 0);
    }
}
