<?php

namespace App\Domain\Ecs\Data;

use App\Domain\Ecs\Enum\PositionChaudiere;

interface PnRepository
{
    public function find_by(PositionChaudiere $position_chaudiere, int $annee_installation, float $pdim): ?Pn;
}
