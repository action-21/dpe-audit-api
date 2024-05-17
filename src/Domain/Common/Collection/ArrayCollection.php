<?php

namespace App\Domain\Common\Collection;

class ArrayCollection implements Collection
{
    public function __construct(protected array $elements = [])
    {
    }

    protected function createFrom(array $elements): static
    {
        return new static($elements);
    }

    public function count(): int
    {
        return \count($this->elements);
    }

    public function to_array(): array
    {
        return $this->elements;
    }

    public function first(): mixed
    {
        return reset($this->elements);
    }

    public function last(): mixed
    {
        return end($this->elements);
    }

    public function key(): string|int|null
    {
        return key($this->elements);
    }

    public function next(): mixed
    {
        return next($this->elements);
    }

    public function current(): mixed
    {
        return current($this->elements);
    }

    public function has(\Closure $p): bool
    {
        return $this->findFirst($p) !== null;
    }

    public function containsKey(string|int $key)
    {
        return isset($this->elements[$key]) || \array_key_exists($key, $this->elements);
    }

    public function contains(mixed $element): bool
    {
        return in_array($element, $this->elements, true);
    }

    public function exists(\Closure $p)
    {
        foreach ($this->elements as $key => $element) {
            if ($p($key, $element)) {
                return true;
            }
        }
        return false;
    }

    public function filter(\Closure $p): static
    {
        return $this->createFrom(array_filter($this->elements, $p, ARRAY_FILTER_USE_BOTH));
    }

    public function map(\Closure $func): static
    {
        return $this->createFrom(array_map($func, $this->elements));
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

    public function values(): array
    {
        return $this->elements;
    }

    public function toArray(): array
    {
        return $this->elements;
    }

    /**
     * @ihneritdoc
     */
    public function offsetExists(mixed $offset): bool
    {
        return $this->containsKey($offset);
    }

    /**
     * @ihneritdoc
     */
    public function offsetGet(mixed $offset): mixed
    {
        return $this->get($offset);
    }

    /**
     * @ihneritdoc
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        if ($offset === null) {
            $this->add($value);
            return;
        }
        if (\is_int($offset) || \is_string($offset)) {
            $this->set($offset, $value);
            return;
        }
    }

    /**
     * @ihneritdoc
     */
    public function offsetUnset(mixed $offset): void
    {
        if (\is_int($offset) || \is_string($offset)) {
            $this->remove($offset);
            return;
        }
    }

    public function indexOf($element): string|int|false
    {
        return array_search($element, $this->elements, true);
    }

    public function get(string|int $key): mixed
    {
        return $this->elements[$key] ?? null;
    }

    public function getKeys(): array
    {
        return array_keys($this->elements);
    }

    public function set(string|int $key, mixed $value): void
    {
        $this->elements[$key] = $value;
    }

    public function add(mixed $element): void
    {
        $this->elements[] = $element;
    }

    public function remove(string|int $key): mixed
    {
        if (!isset($this->elements[$key]) && !array_key_exists($key, $this->elements)) {
            return null;
        }

        $removed = $this->elements[$key];
        unset($this->elements[$key]);

        return $removed;
    }

    public function removeElement(mixed $element)
    {
        $key = array_search($element, $this->elements, true);

        if ($key === false) {
            return false;
        }

        unset($this->elements[$key]);

        return true;
    }

    public function isEmpty(): bool
    {
        return empty($this->elements);
    }

    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->elements);
    }
}
