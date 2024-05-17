<?php

namespace App\Domain\Photovoltaique\Table;

interface KRepository
{
    public function find(int $id): ?K;
    public function find_by(float $inclinaison, float $orientation): ?K;
}
