<?php

namespace App\Domain\MasqueProche\Table;

use App\Domain\Common\Enum\Orientation;
use App\Domain\MasqueProche\Enum\TypeMasqueProche;

interface Fe1Repository
{
    public function find(int $id): ?Fe1;
    public function find_by(TypeMasqueProche $type_masque_proche, ?Orientation $orientation, ?float $avancee): ?Fe1;

    public function search_by(
        ?TypeMasqueProche $type_masque_proche = null,
        ?Orientation $orientation = null,
        ?float $avancee = null,
        ?int $tv_coef_masque_proche_id = null
    ): Fe1Collection;
}
