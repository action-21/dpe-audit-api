<?php

namespace App\Domain\PontThermique\Table;

use App\Domain\Common\Enum\Enum;
use App\Domain\PontThermique\Enum\TypeLiaison;

interface KptRepository
{
    public function find(int $id): ?Kpt;

    public function find_by(
        TypeLiaison $type_liaison,
        ?Enum $type_isolation_mur,
        ?Enum $type_isolation_plancher,
        ?Enum $type_pose_ouverture,
        ?bool $presence_retour_isolation,
        ?int $largeur_dormant,
    ): ?Kpt;
}
