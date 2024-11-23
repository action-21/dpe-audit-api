<?php

namespace App\Domain\Production\Data;

final class Kpv
{
    public function __construct(public readonly float $kpv) {}
}
