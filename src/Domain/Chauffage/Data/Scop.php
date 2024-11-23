<?php

namespace App\Domain\Chauffage\Data;

final class Scop
{
    public function __construct(
        public readonly ?float $cop,
        public readonly ?float $scop,
    ) {}
}
