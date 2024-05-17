<?php

namespace App\Domain\Lnc\Table;

interface BRepository
{
    public function find(int $id): ?B;
    public function find_by(
        float $uvue,
        ?bool $isolation_aiu,
        ?bool $isolation_aue,
        ?float $surface_aiu,
        ?float $surface_aue,
    ): ?B;
}
