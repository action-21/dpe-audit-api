<?php

namespace App\Domain\Ecs\Data;

use App\Domain\Ecs\Enum\{EnergieGenerateur, TypeGenerateur};

interface PauxRepository
{
    public function find_by(
        TypeGenerateur $type_generateur,
        EnergieGenerateur $energie_generateur,
        ?bool $presence_ventouse,
    ): ?Paux;
}
