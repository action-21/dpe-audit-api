<?php

namespace App\Domain\Common\Type;

use Symfony\Component\Uid\{AbstractUid, Uuid};

final class Id extends AbstractUid
{
    public function __construct(public readonly string $value) {}

    public static function fromString(string $uid): static
    {
        return new self(value: $uid);
    }

    public static function from(string $value): static
    {
        return new self(value: $value);
    }

    public static function create(): static
    {
        return new self(value: Uuid::v7()->toRfc4122());
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
