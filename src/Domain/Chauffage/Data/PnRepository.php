<?php

namespace App\Domain\Chauffage\Data;

use App\Domain\Chauffage\Enum\PositionChaudiere;

interface PnRepository
{
    public function find_by(PositionChaudiere $position_chaudiere, int $annee_installation, float $pdim): ?Pn;
}
