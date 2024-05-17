<?php

namespace App\Domain\Ecs\Table;

final class Fecs
{
    public function __construct(
        public readonly int $id,
        public readonly float $fecs,
    ) {
    }

    public function id(): int
    {
        return $this->id;
    }

    public function fecs(): float
    {
        return $this->fecs;
    }
}
