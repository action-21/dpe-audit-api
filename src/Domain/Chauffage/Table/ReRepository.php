<?php

namespace App\Domain\Chauffage\Table;

use App\Domain\Chauffage\Enum\{TypeEmission, TypeGenerateur};

interface ReRepository
{
    public function find(int $id): ?Re;
    public function find_by(TypeEmission $type_emission, TypeGenerateur $type_generateur): ?Re;
}
