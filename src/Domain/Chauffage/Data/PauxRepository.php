<?php

namespace App\Domain\Chauffage\Data;

use App\Domain\Chauffage\Enum\{EnergieGenerateur, TypeGenerateur};

interface PauxRepository
{
    public function find_by(TypeGenerateur $type_generateur, EnergieGenerateur $energie_generateur, ?bool $presence_ventouse): ?Paux;
}
