<?php

namespace App\Domain\Common\ValueObject;

final class Pourcentage
{
    public function __construct(public readonly int|float $value) {}

    public static function from(int|float $value): self
    {
        return new self($value);
    }

    public static function from_decimal(float $value): self
    {
        return static::from($value * 100);
    }

    #[\Deprecated]
    public function number(): int|float
    {
        return $this->value / 100;
    }

    public function decimal(): float
    {
        return $this->value / 100;
    }

    public function value(): int|float
    {
        return $this->value;
    }
}
