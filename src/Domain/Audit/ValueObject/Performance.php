<?php

namespace App\Domain\Audit\ValueObject;

use App\Domain\Audit\Enum\Etiquette;

final class Performance
{
    public function __construct(
        public readonly float $consommation_ef,
        public readonly float $consommation_ep,
        public readonly float $emission,
        public readonly Etiquette $etiquette_energie,
        public readonly Etiquette $etiquette_climat,
    ) {}
}
