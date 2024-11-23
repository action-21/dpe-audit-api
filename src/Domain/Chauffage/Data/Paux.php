<?php

namespace App\Domain\Chauffage\Data;

final class Paux
{
    public function __construct(
        public readonly float $g,
        public readonly float $h,
        public readonly ?float $pn_max,
    ) {}
}
