<?php

namespace App\Domain\Chauffage\Data;

use App\Domain\Chauffage\Enum\{EnergieGenerateur, LabelGenerateur, TypeGenerateur};

interface RgRepository
{
    public function find_by(
        TypeGenerateur $type_generateur,
        EnergieGenerateur $energie_generateur,
        ?LabelGenerateur $label_generateur,
        int $annee_installation_generateur,
    ): ?Rg;
}
