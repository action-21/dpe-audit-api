<?php

namespace App\Domain\Chauffage\Table;

use App\Domain\Chauffage\Enum\{TypeGenerateur};

interface RgRepository
{
    public function find(int $id): ?Rg;
    public function find_by(TypeGenerateur $type_generateur, ?int $annee_installation_generateur): ?Rg;
}
