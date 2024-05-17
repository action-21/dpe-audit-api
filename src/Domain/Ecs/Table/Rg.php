<?php

namespace App\Domain\Ecs\Table;

final class Rg
{
    public function __construct(
        public readonly int $id,
        public readonly float $rg,
    ) {
    }

    public function id(): int
    {
        return $this->id;
    }
}
