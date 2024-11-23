<?php

namespace App\Domain\Ventilation\Data;

final class Pvent
{
    public function __construct(
        public readonly float $ratio_utilisation,
        public readonly float $pvent_moy,
        public readonly float $pvent,
    ) {}
}
