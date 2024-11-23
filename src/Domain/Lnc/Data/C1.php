<?php

namespace App\Domain\Lnc\Data;

use App\Domain\Common\Enum\Mois;

final class C1
{
    public function __construct(public readonly Mois $mois, public readonly float $c1,) {}
}
