<?php

namespace App\Domain\Common\ValueObject;

use Symfony\Component\Uid\{AbstractUid, Uuid};

final class Id implements \Stringable
{
    public function __construct(public readonly string $value) {}

    public static function from(string $value): static
    {
        return new self(value: $value);
    }

    public static function create(): static
    {
        return new self(value: Uuid::v7()->toRfc4122());
    }

    public function compare(Id $id): bool
    {
        return $this->value === $id->value;
    }

    public static function isValid(string $uid): bool
    {
        return Uuid::isValid($uid);
    }

    public function toBinary(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
