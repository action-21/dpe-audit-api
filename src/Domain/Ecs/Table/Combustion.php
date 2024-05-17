<?php

namespace App\Domain\Ecs\Table;

final class Combustion
{
    public function __construct(
        public readonly int $id,
        public readonly string $rpn,
        public readonly ?string $qp0,
        public readonly ?float $pn_max,
        public readonly ?float $pveil,
    ) {
    }

    public function id(): int
    {
        return $this->id;
    }
}
