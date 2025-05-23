<?php

namespace App\Domain\Common\ValueObject;

use App\Domain\Common\Enum\Orientation as Enum;
use Webmozart\Assert\Assert;

final class Orientation implements \Stringable
{
    public function __construct(public readonly float $value) {}

    public static function from(float $value): self
    {
        Assert::greaterThanEq($value, 0);
        Assert::lessThanEq($value, 360);
        return new self($value);
    }

    public static function from_enum_orientation_id(int $id): ?self
    {
        return match ($id) {
            1 => self::from(180),
            2 => self::from(0),
            3 => self::from(90),
            4 => self::from(270),
            default => null,
        };
    }

    public function compare(Enum $orientation): bool
    {
        return $orientation === Enum::from_azimut($this->value);
    }

    public function minus(float $azimut): float
    {
        return fmod($this->value - $azimut + 360, 360);
    }

    public function plus(float $azimut): float
    {
        $orientation = fmod($this->value + $azimut, 360);
        return $orientation < 0 ? $orientation += 360 : $orientation;
    }

    public function between(float $azimut1, float $azimut2): bool
    {
        return $azimut1 < $azimut2
            ? $this->value >= $azimut1 && $this->value <= $azimut2
            : $this->value >= $azimut1 || $this->value <= $azimut2;
    }

    public function enum(): Enum
    {
        return Enum::from_azimut($this->value);
    }

    public function value(): float
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }
}
