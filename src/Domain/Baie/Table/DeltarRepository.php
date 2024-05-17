<?php

namespace App\Domain\Baie\Table;

use App\Domain\Baie\Enum\TypeFermeture;

interface DeltarRepository
{
    public function find(int $id): ?Deltar;
    public function find_by(TypeFermeture $type_fermeture): ?Deltar;
}
