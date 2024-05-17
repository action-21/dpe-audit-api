<?php

namespace App\Domain\Mur\Table;

use App\Domain\Mur\Enum\TypeMur;

interface Umur0Repository
{
    public function find(int $id): ?Umur0;
    public function find_by(TypeMur $type_mur, ?float $epaisseur_structure): ?Umur0;
    public function search(int $id): Umur0Collection;
    public function search_by(TypeMur $type_mur): Umur0Collection;
}
