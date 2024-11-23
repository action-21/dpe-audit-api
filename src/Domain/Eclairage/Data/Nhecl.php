<?php

namespace App\Domain\Eclairage\Data;

use App\Domain\Common\Enum\Mois;

final class Nhecl
{
    public function __construct(public readonly Mois $mois, public readonly float $nhecl,) {}
}
