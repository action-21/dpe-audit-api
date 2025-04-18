<?php

namespace App\Domain\Common\ValueObject;

use Webmozart\Assert\Assert;

final class Inclinaison implements \Stringable
{
    public function __construct(public readonly float $value) {}

    public static function from(float $value): self
    {
        Assert::greaterThanEq($value, 0);
        Assert::lessThanEq($value, 90);
        return new self($value);
    }

    public static function from_enum_inclinaison_vitrage_id(int $id): ?self
    {
        return match ($id) {
            1 => self::from(15),
            2 => self::from(50),
            3 => self::from(90),
            4 => self::from(0),
            default => null,
        };
    }

    public static function from_enum_inclinaison_pv_id(int $id): ?self
    {
        return match ($id) {
            1 => self::from(10),
            2 => self::from(30),
            3 => self::from(60),
            4 => self::from(80),
            default => null,
        };
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
