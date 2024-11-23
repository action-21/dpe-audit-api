<?php

namespace App\Domain\Enveloppe\Data;

use App\Domain\Common\Enum\Enum;

interface Q4paConvRepository
{
    public function find_by(
        Enum $type_batiment,
        int $annee_construction,
        ?bool $presence_joints_menuiserie,
        ?bool $isolation_murs_plafonds,
    ): ?Q4paConv;
}
