<?php

namespace App\Domain\PontThermique\Data;

final class Kpt
{
    public function __construct(public readonly float $kpt) {}
}
