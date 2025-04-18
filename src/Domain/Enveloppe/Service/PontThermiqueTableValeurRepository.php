<?php

namespace App\Domain\Enveloppe\Service;

use App\Domain\Enveloppe\Enum\{EtatIsolation, TypeIsolation, TypePose};
use App\Domain\Enveloppe\Enum\PontThermique\TypeLiaison;

interface PontThermiqueTableValeurRepository
{
    public function kpt(
        TypeLiaison $type_liaison,
        EtatIsolation $etat_isolation_mur,
        ?TypeIsolation $type_isolation_mur,
        ?EtatIsolation $etat_isolation_plancher,
        ?TypeIsolation $type_isolation_plancher,
        ?TypePose $type_pose,
        ?bool $presence_retour_isolation,
        ?float $largeur_dormant,
    ): ?float;
}
