<?php

namespace App\Database\Local;

class XMLTableElement extends \SimpleXMLElement
{
    public function id(): ?int
    {
        return isset($this['id']) ? (int) $this['id'] : null;
    }

    public function __get(string $property): self
    {
        return $this->{$property};
    }
}
