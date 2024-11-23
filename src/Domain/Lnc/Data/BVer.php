<?php

namespace App\Domain\Lnc\Data;

use App\Domain\Common\Enum\Orientation;

final class BVer
{
    public function __construct(
        public readonly Orientation $orientation,
        public readonly bool $isolation_paroi,
        public readonly float $bver,
    ) {}
}
