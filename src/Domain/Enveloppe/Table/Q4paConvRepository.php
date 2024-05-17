<?php

namespace App\Domain\Enveloppe\Table;

use App\Domain\Batiment\Enum\TypeBatiment;

interface Q4paConvRepository
{
    public function find(int $id): ?Q4paConv;
    public function find_by(
        TypeBatiment $type_batiment,
        int $annee_construction,
        ?bool $presence_joints_menuiserie,
        ?bool $isolation_murs_plafonds,
    ): ?Q4paConv;
}
