<?php

namespace App\Domain\Baie\ValueObject;

use App\Domain\Common\Enum\Orientation;
use App\Domain\Baie\Data\{Fe1, Fe1Collection};

final class EnsoleillementMasqueProche
{
    public function __construct(public readonly Fe1Collection $fe1s) {}

    public static function create(Fe1Collection $fe1s): self
    {
        return new self(fe1s: $fe1s);
    }

    public function fe1(?Orientation $orientation_facade): float
    {
        $collection = $this->fe1s->filter_by_orientation(orientation_façade: $orientation_facade);
        return $collection->count() ? $collection->min() : 1;
    }
}
