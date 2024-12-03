<?php

namespace App\Domain\Mur\Data;

use App\Domain\Mur\Enum\TypeMur;

interface Umur0Repository
{
    public function search_by(TypeMur $type_mur, int $annee_construction): Umur0Collection;
}
