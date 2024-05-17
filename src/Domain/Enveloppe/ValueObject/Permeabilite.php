<?php

namespace App\Domain\Enveloppe\ValueObject;

use App\Domain\Enveloppe\Enum\Exposition;

final class Permeabilite
{
    public function __construct(
        public readonly Exposition $exposition,
        public readonly ?Q4PaConv $q4pa_conv,
    ) {
    }
}
