<?php

namespace App\Domain\Enveloppe\ValueObject;

use App\Domain\Enveloppe\Enum\TypeDeperdition;

/**
 * @property Deperdition[] $values
 */
final class Deperditions
{
    public function __construct(public readonly array $values) {}

    public static function create(Deperdition ...$values): self
    {
        return new self($values);
    }

    public function merge(self $value): self
    {
        return static::create(...array_merge($this->values, $value->values));
    }

    public function get(?TypeDeperdition $type = null): float
    {
        $values = $type ? array_filter($this->values, fn(Deperdition $value) => $value->type === $type) : $this->values;
        return array_reduce($values, fn(float $dp, Deperdition $value) => $dp + $value->deperdition, 0);
    }

    /**
     * @return Deperdition[]
     */
    public function values(): array
    {
        return $this->values;
    }
}
