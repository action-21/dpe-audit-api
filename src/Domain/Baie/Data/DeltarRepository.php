<?php

namespace App\Domain\Baie\Data;

use App\Domain\Baie\Enum\TypeFermeture;

interface DeltarRepository
{
    public function find_by(TypeFermeture $type_fermeture): ?Deltar;
}
