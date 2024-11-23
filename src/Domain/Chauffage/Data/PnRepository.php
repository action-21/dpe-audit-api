<?php

namespace App\Domain\Chauffage\Data;

use App\Domain\Chauffage\Enum\TypeChaudiere;

interface PnRepository
{
    public function find_by(TypeChaudiere $type_chaudiere, int $annee_installation, float $pdim): ?Pn;
}
