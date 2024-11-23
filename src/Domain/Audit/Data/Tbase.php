<?php

namespace App\Domain\Audit\Data;

final class Tbase
{
    public function __construct(public readonly float $tbase) {}
}
