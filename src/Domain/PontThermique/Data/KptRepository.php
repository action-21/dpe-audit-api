<?php

namespace App\Domain\PontThermique\Data;

use App\Domain\PontThermique\Enum\{TypeIsolation, TypeLiaison, TypePose};

interface KptRepository
{
    public function find_by(
        TypeLiaison $type_liaison,
        ?TypeIsolation $type_isolation_mur,
        ?TypeIsolation $type_isolation_plancher,
        ?TypePose $type_pose_ouverture,
        ?bool $presence_retour_isolation,
        ?int $largeur_dormant,
    ): ?Kpt;
}
