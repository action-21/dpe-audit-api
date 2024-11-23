<?php

namespace App\Domain\Ecs\Data;

final class Combustion
{
    public function __construct(
        public readonly string $rpn,
        public readonly string $qp0,
        public readonly ?float $pn_max,
        public readonly ?float $pveilleuse,
    ) {}
}
