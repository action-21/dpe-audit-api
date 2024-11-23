<?php

namespace App\Domain\Ecs\Data;

use App\Domain\Ecs\Enum\CategorieGenerateur;

interface PauxRepository
{
    public function find_by(CategorieGenerateur $categorie_generateur, ?bool $presence_ventouse): ?Paux;
}
