<?php

namespace App\Domain\Chauffage\Data;

use App\Domain\Chauffage\Enum\CategorieGenerateur;

interface PauxRepository
{
    public function find_by(CategorieGenerateur $categorie_generateur, ?bool $presence_ventouse): ?Paux;
}
