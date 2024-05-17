<?php

namespace App\Domain\Ecs\Table;

use App\Domain\Ecs\Enum\TypeGenerateur;

interface CrRepository
{
    public function find(int $id): ?Cr;
    public function find_by(TypeGenerateur $type_generateur, float $volume_stockage): ?Cr;
}