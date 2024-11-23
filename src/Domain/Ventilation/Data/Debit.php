<?php

namespace App\Domain\Ventilation\Data;

final class Debit
{
    public function __construct(
        public readonly float $qvarep_conv,
        public readonly float $qvasouf_conv,
        public readonly float $smea_conv,
    ) {}
}
