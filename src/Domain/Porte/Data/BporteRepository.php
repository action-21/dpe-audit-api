<?php

namespace App\Domain\Porte\Data;

use App\Domain\Porte\Enum\Mitoyennete;

interface BporteRepository
{
    public function find_by(Mitoyennete $mitoyennete): ?Bporte;
}
