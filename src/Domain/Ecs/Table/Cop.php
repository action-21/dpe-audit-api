<?php

namespace App\Domain\Ecs\Table;

final class Cop
{
    public function __construct(
        public readonly int $id,
        public readonly float $cop,
    ) {
    }

    public function id(): int
    {
        return $this->id;
    }
}
