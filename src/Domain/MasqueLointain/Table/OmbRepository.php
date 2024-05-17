<?php

namespace App\Domain\MasqueLointain\Table;

use App\Domain\Common\Enum\Orientation;
use App\Domain\MasqueLointain\Enum\SecteurOrientation;

interface OmbRepository
{
    public function find(int $id): ?Omb;
    public function find_by(SecteurOrientation $secteur_orientation, Orientation $orientation, float $hauteur_alpha): ?Omb;

    public function search_by(
        ?SecteurOrientation $secteur_orientation = null,
        ?Orientation $orientation = null,
        ?float $hauteur_alpha = null,
        ?int $tv_coef_masque_lointain_non_homogene_id = null,
    ): OmbCollection;
}
