<?php

namespace App\Domain\Chauffage\Data;

final class Combustion
{
    public function __construct(
        public readonly string $rpn,
        public readonly ?float $pn_max,
        public readonly ?string $rpint,
        public readonly ?string $qp0,
        public readonly ?float $pveilleuse,
    ) {}
}
