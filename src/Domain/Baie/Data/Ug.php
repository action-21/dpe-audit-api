<?php

namespace App\Domain\Baie\Data;

final class Ug
{
    public function __construct(public readonly ?float $epaisseur_lame, public readonly float $ug) {}
}
