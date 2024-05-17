<?php

namespace App\Domain\MasqueLointain\Table;

use App\Domain\Common\Enum\Orientation;

interface Fe2Repository
{
    public function find(int $id): ?Fe2;
    public function find_by(Orientation $orientation, float $hauteur_alpha): ?Fe2;

    public function search_by(
        ?Orientation $orientation = null,
        ?float $hauteur_alpha = null,
        ?int $tv_coef_masque_lointain_homogene_id = null,
    ): Fe2Collection;
}
