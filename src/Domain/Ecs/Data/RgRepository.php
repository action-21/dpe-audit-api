<?php

namespace App\Domain\Ecs\Data;

use App\Domain\Ecs\Enum\{CategorieGenerateur, EnergieGenerateur};

interface RgRepository
{
    public function find_by(CategorieGenerateur $categorie_generateur, EnergieGenerateur $energie_generateur,): ?Rg;
}
