<?php

namespace App\Domain\Lnc\Data;

interface BRepository
{
    public function find_by(float $uvue, float $aiu, float $aue, bool $isolation_aiu, bool $isolation_aue): ?B;
}
