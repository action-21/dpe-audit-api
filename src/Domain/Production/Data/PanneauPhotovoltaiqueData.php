<?php

namespace App\Domain\Production\Data;

use Webmozart\Assert\Assert;

final class PanneauPhotovoltaiqueData
{
    public function __construct(
        public readonly ?float $kpv,
        public readonly ?float $surface,
        public readonly ?float $production,
    ) {}

    public static function create(
        ?float $kpv = null,
        ?float $surface = null,
        ?float $production = null,
    ): self {
        Assert::nullOrGreaterThanEq($kpv, 0);
        Assert::nullOrGreaterThan($surface, 0);
        Assert::nullOrGreaterThanEq($production, 0);
        return new self(
            kpv: $kpv,
            surface: $surface,
            production: $production,
        );
    }

    public function with(
        ?float $kpv = null,
        ?float $surface = null,
        ?float $production = null,
    ): self {
        return self::create(
            kpv: $kpv ?? $this->kpv,
            surface: $surface ?? $this->surface,
            production: $production ?? $this->production,
        );
    }
}
