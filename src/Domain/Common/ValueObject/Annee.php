<?php

namespace App\Domain\Common\ValueObject;

use Webmozart\Assert\Assert;

final class Annee implements \Stringable
{
    public function __construct(public readonly int $value) {}

    public static function from(int $value): self
    {
        Assert::lessThanEq($value, (int) (new \DateTime())->format('Y'));
        return new self($value);
    }

    public static function from_periode_installation_emetteur_id(int $id): self
    {
        return match ($id) {
            1 => self::from(1980),
            2 => self::from(2000),
            3 => self::from((int) date('Y')),
        };
    }

    public static function from_periode_installation_ecs_thermo_id(int $id): self
    {
        return match ($id) {
            1 => self::from(2009),
            2 => self::from(2014),
            3 => self::from((int) date('Y')),
        };
    }

    public function less_than(int $value): bool
    {
        return $this->value < $value;
    }

    public function less_than_or_equal(int $value): bool
    {
        return $this->value <= $value;
    }

    public function greater_than(int $value): bool
    {
        return $this->value > $value;
    }

    public function greater_than_or_equal(int $value): bool
    {
        return $this->value >= $value;
    }

    public function eq(int $value): bool
    {
        return $this->value === $value;
    }

    public function value(): int
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }
}
