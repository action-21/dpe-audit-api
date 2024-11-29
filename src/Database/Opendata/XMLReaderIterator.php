<?php

namespace App\Database\Opendata;

abstract class XMLReaderIterator implements \Iterator
{
    private int $position = 0;

    public function __construct(
        /** @var XMLElement[] */
        private array $array = [],
    ) {}

    /** @param XMLElement[] $xml */
    public static function from(array $xml): static
    {
        return new static($xml);
    }

    /**
     * @param XMLElement[] $xml
     */
    public function read(array $xml): static
    {
        $this->array = $xml;
        return $this;
    }

    public function xml(): XMLElement
    {
        return $this->array[$this->position];
    }

    public function current(): static
    {
        return $this;
    }

    public function key(): int
    {
        return $this->position;
    }

    public function next(): void
    {
        ++$this->position;
    }

    public function rewind(): void
    {
        $this->position = 0;
    }

    public function valid(): bool
    {
        return isset($this->array[$this->position]);
    }

    public function count(): int
    {
        return \count($this->array);
    }
}
