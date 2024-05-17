<?php

namespace App\Domain\Common\Table;

/**
 * @property TableValue[] $elements
 */
abstract class TableValueCollection
{
    public function __construct(protected array $elements = [])
    {
    }

    public function count(): int
    {
        return \count($this->elements);
    }

    public function first(): mixed
    {
        return reset($this->elements);
    }

    /**
     * Tri par ordre croissant
     */
    public function sort(): static
    {
        $elements = [...$this->elements];

        usort($elements, fn (TableValue $a, TableValue $b): int => $b->id() - $a->id());

        return new static($elements);
    }

    /**
     * Tri par ordre décroissant
     */
    public function asort(): static
    {
        $elements = [...$this->elements];

        usort($elements, fn (TableValue $a, TableValue $b): int => $a->id() - $b->id());

        return new static($elements);
    }

    public function usort(\Closure $p): static
    {
        $elements = [...$this->elements];

        usort($elements, $p);

        return new static($elements);
    }

    public function slice(int $offset, ?int $length,): static
    {
        return new static(\array_slice($this->elements, $offset, $length));
    }

    public function filter(\Closure $p): static
    {
        return new static(array_filter($this->elements, $p, ARRAY_FILTER_USE_BOTH));
    }

    public function map(\Closure $func): array
    {
        return array_map($func, $this->elements);
    }

    public function reduce(\Closure $func, $initial = null): mixed
    {
        return array_reduce($this->elements, $func, $initial);
    }

    public function findFirst(\Closure $p): mixed
    {
        foreach ($this->elements as $key => $element) {
            if ($p($key, $element)) {
                return $element;
            }
        }
        return null;
    }

    /** @return int[] */
    public function toIds(): array
    {
        return \array_map(fn (TableValue $item): int => $item->id(), $this->elements);
    }

    public function to_array(): array
    {
        return $this->elements;
    }
}
