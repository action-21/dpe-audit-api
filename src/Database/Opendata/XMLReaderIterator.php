<?php

namespace App\Database\Opendata;

abstract class XMLReaderIterator implements \Iterator
{
    /** @var XMLElement[] */
    protected array $array = [];
    protected int $position = 0;

    public function get(): XMLElement
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
}
