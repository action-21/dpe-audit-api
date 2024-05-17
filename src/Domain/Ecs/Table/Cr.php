<?php

namespace App\Domain\Ecs\Table;

final class Cr
{
    public function __construct(
        public readonly int $id,
        public readonly float $cr,
    ) {
    }

    public function id(): int
    {
        return $this->id;
    }

    public function cr(): float
    {
        return $this->cr;
    }
}
