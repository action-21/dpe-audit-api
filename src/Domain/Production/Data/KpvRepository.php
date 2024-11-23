<?php

namespace App\Domain\Production\Data;

interface KpvRepository
{
    public function find_by(float $orientation, float $inclinaison): ?Kpv;
}
