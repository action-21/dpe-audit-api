<?php

namespace App\Domain\Enveloppe\ValueObject;

use App\Domain\Enveloppe\Enum\TypeDeperdition;
use Webmozart\Assert\Assert;

/**
 * @property Deperdition[] $values
 */
final class Deperditions
{
    public function __construct(public readonly array $values) {}

    public static function create(Deperdition ...$values): self
    {
        Assert::uniqueValues(array_map(fn(Deperdition $value) => $value->type->id(), $values));
        return new self($values);
    }

    public function add(Deperdition $value): self
    {
        $values = [Deperdition::create(
            type: $value->type,
            deperdition: $value->deperdition + $this->get(type: $value->type),
        )];

        foreach ($this->values as $item) {
            if ($item->type === $value->type) {
                continue;
            }
            $values[] = $item;
        }
        return static::create(...$values);
    }

    public function merge(self $value): self
    {
        foreach ($this->values as $item) {
            $value = $value->add($item);
        }
        return $value;
    }

    public function get(TypeDeperdition ...$types): float
    {
        $values = $types ? array_filter($this->values, fn(Deperdition $value) => in_array($value->type, $types)) : $this->values;
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
