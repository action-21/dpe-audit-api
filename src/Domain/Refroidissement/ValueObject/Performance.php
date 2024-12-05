<?php

namespace App\Domain\Refroidissement\ValueObject;

use Webmozart\Assert\Assert;

final class Performance
{
    public function __construct(public readonly float $eer) {}

    public static function create(float $eer): self
    {
        Assert::greaterThan($eer, 0);
        return new self(eer: $eer);
    }
}
