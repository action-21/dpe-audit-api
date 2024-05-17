<?php

namespace App\Application\Batiment\View;

class EcsView
{
    public function __construct(
        public readonly float $cecs,
        public readonly float $cecs_j,
        public readonly float $becs,
        public readonly float $becs_j,
    ) {
    }
}
