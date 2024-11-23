<?php

namespace App\Domain\PlancherBas\Data;

final class Ue
{
    public function __construct(
        public readonly float $_2sp,
        public readonly float $upb,
        public readonly float $ue,
    ) {}
}
