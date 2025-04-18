<?php

namespace App\Domain\Production;

use Webmozart\Assert\Assert;

final class ProductionData
{
    public function __construct(
        public readonly ?float $production,
    ) {}

    public static function create(?float $production = null): self
    {
        Assert::nullOrGreaterThanEq($production, 0);
        return new self(production: $production);
    }

    public function with(?float $production = null): self
    {
        return self::create(
            production: $production ?? $this->production,
        );
    }
}
