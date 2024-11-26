<?php

namespace App\Domain\Eclairage\Data;

use App\Domain\Common\Enum\Mois;

final class Nhj
{
    public function __construct(public readonly Mois $mois, public readonly float $nhj,) {}
}
