<?php

namespace App\Application\Batiment\View;

use App\Domain\Batiment\BatimentEngine;

class OccupationView
{
    public function __construct(
        public readonly null|float $nadeq = null,
        public readonly null|float $nmax = null,
    ) {
    }

    public static function from_engine(BatimentEngine $engine): self
    {
        return new self(
            nadeq: $engine->occupation()->nadeq(),
            nmax: $engine->occupation()->nmax(),
        );
    }
}
