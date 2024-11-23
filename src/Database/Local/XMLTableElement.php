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

    public function get(string $property): self
    {
        return $this->{$property};
    }

    public function strval(): ?string
    {
        return '' === (string) $this ? null : (string) $this;
    }

    public function intval(): ?int
    {
        return '' === (string) $this ? null : (int) $this;
    }

    public function floatval(): ?float
    {
        return '' === (string) $this ? null : (float) $this;
    }

    public function boolval(): ?bool
    {
        return '' === (string) $this ? null : (bool) $this;
    }
}
