<?php

namespace App\Domain\Enveloppe\ValueObject;

use App\Domain\Enveloppe\Enum\{EtatIsolation, TypeParoi};

/**
 * @property SurfaceDeperditive[] $values
 */
final class SurfacesDeperditives
{
    public function __construct(public readonly array $values) {}

    public static function create(SurfaceDeperditive ...$values): self
    {
        return new self($values);
    }

    public function merge(self $value): self
    {
        return static::create(...array_merge($this->values, $value->values));
    }

    public function get(?TypeParoi $type = null, ?EtatIsolation $isolation = null): float
    {
        $values = $type ? array_filter($this->values, fn(SurfaceDeperditive $value) => $value->type === $type) : $this->values;
        $values = $isolation ? array_filter($values, fn(SurfaceDeperditive $value) => $value->isolation === $isolation) : $values;
        return array_reduce($values, fn(float $sdep, SurfaceDeperditive $value) => $sdep + $value->sdep, 0);
    }

    /**
     * @return SurfaceDeperditive[]
     */
    public function values(): array
    {
        return $this->values;
    }
}
