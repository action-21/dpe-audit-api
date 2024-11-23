<?php

namespace App\Domain\Chauffage\Data;

use App\Domain\Chauffage\Enum\{TypeEmission, TypeIntermittence};
use App\Domain\Common\Enum\Enum;

interface I0Repository
{
    public function find_by(
        Enum $type_batiment,
        TypeEmission $type_emission,
        TypeIntermittence $type_intermittence,
        bool $chauffage_central,
        bool $regulation_terminale,
        bool $chauffage_collectif,
        bool $inertie_lourde,
        ?bool $comptage_individuel,
    ): ?I0;
}
