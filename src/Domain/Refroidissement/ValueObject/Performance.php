<?php

namespace App\Domain\Refroidissement\ValueObject;

use App\Domain\Common\Service\Assert;

final class Performance
{
    public function __construct(public readonly float $eer) {}

    public static function create(float $eer): self
    {
        Assert::positif($eer);
        return new self(eer: $eer);
    }
}
