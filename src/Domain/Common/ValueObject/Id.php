<?php

namespace App\Domain\Common\ValueObject;

final class Id implements \Stringable
{
    public function __construct(public readonly string $value)
    {
    }

    public static function from(string $value): self
    {
        return new self(value: $value);
    }

    public static function create(): self
    {
        return new self(value: \Symfony\Component\Uid\Uuid::v7()->toRfc4122());
    }

    public function compare(Id $id): bool
    {
        return (string) $this === (string) $id;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
