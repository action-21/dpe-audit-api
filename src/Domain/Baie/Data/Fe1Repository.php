<?php

namespace App\Domain\Baie\Data;

use App\Domain\Baie\Enum\TypeMasqueProche;
use App\Domain\Common\Enum\Orientation;

interface Fe1Repository
{
    public function find_by(
        TypeMasqueProche $type_masque_proche,
        ?float $avancee_masque,
        ?Orientation $orientation_baie,
    ): ?Fe1;
}
