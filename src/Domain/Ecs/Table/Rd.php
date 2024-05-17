<?php

namespace App\Domain\Ecs\Table;

final class Rd
{
    public function __construct(
        public readonly int $id,
        public readonly float $rd,
    ) {
    }

    public function id(): int
    {
        return $this->id;
    }

    public function rd(): float
    {
        return $this->rd;
    }
}
