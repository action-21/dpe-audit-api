<?php

namespace App\Domain\Enveloppe\ValueObject;

use App\Domain\Enveloppe\Enum\{EtatIsolation, TypeParoi};
use Webmozart\Assert\Assert;

/**
 * @property SurfaceDeperditive[] $values
 */
final class SurfacesDeperditives
{
    public function __construct(public readonly array $values) {}

    public static function create(SurfaceDeperditive ...$values): self
    {
        Assert::uniqueValues(array_map(
            fn(SurfaceDeperditive $value) => "{$value->type->id()}{$value->isolation->id()}",
            $values
        ));

        return new self($values);
    }

    public function add(SurfaceDeperditive $value): self
    {
        $values = [SurfaceDeperditive::create(
            type: $value->type,
            isolation: $value->isolation,
            sdep: $value->sdep + $this->get(type: $value->type, isolation: $value->isolation),
        )];

        foreach ($this->values as $item) {
            if ($item->type === $value->type && $item->isolation === $value->isolation) {
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
