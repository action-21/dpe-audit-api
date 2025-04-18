<?php

namespace App\Database\Local;

final class XMLTableElement extends \SimpleXMLElement
{
    public function id(): ?int
    {
        return isset($this['id']) ? (int) $this['id'] : null;
    }

    public function __get(string $property): self
    {
        return $this->{$property};
    }

    public function isEmpty(): bool
    {
        return (string) $this === '';
    }

    public function get(string $property): self
    {
        return $this->{$property};
    }

    public function strval(?string $property = null): ?string
    {
        $value = $property ? $this->get($property) : $this;
        return '' === (string) $value ? null : (string) $value;
    }

    public function intval(?string $property = null): ?int
    {
        $value = $property ? $this->get($property) : $this;
        return '' === (string) $value ? null : (int) $value;
    }

    public function floatval(?string $property = null): ?float
    {
        $value = $property ? $this->get($property) : $this;
        return '' === (string) $value ? null : (float) $value;
    }

    public function boolval(?string $property = null): ?bool
    {
        $value = $property ? $this->get($property) : $this;
        return '' === (string) $value ? null : (bool)(int) $value;
    }

    public function to(\Closure $callback): mixed
    {
        return $callback($this);
    }
}
