<?php

namespace App\Database\Local;

final class XMLTableCollection implements \Countable, \IteratorAggregate
{
    public function __construct(private array $values) {}

    public function from(array $values): static
    {
        return new static($values);
    }

    public function count(): int
    {
        return \count($this->values);
    }

    public function first(): ?XMLTableElement
    {
        return $this->count() ? current($this->values) : null;
    }

    public function last(): ?XMLTableElement
    {
        return $this->count() ? end($this->values) : null;
    }

    public function usort(string $name, float $value): static
    {
        if (null === $value) {
            return $this;
        }
        $elements = [...$this->values];

        usort(
            $elements,
            fn(XMLTableElement $a, XMLTableElement $b): int => \round(\abs($a->floatval($name) - $value) - \abs($b->floatval($name) - $value))
        );

        return new static($elements);
    }

    public function slice(int $offset, ?int $length): static
    {
        return new static(\array_slice($this->values, $offset, $length));
    }

    public function find(string $name, ?string $value): ?XMLTableElement
    {
        return array_find($this->values, fn(XMLTableElement $element): bool => $element->strval($name) === $value);
    }

    public function filter(\Closure $p): static
    {
        return $this->from(array_filter($this->values, $p, ARRAY_FILTER_USE_BOTH));
    }

    public function map(\Closure $p): static
    {
        return $this->from(array_map($p, $this->values));
    }

    public function reduce(\Closure $func, mixed $initial = 0): mixed
    {
        return array_reduce($this->values, $func, $initial);
    }

    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->values);
    }

    public function values(): array
    {
        return $this->values;
    }
}
