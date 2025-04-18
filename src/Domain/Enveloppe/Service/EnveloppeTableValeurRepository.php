<?php

namespace App\Domain\Enveloppe\Service;

use App\Domain\Audit\Enum\TypeBatiment;
use App\Domain\Common\ValueObject\Annee;
use App\Domain\Enveloppe\Enum\EtatIsolation;

interface EnveloppeTableValeurRepository
{
    public function q4pa_conv(
        TypeBatiment $type_batiment,
        Annee $annee_construction,
        bool $presence_joints_menuiserie,
        EtatIsolation $isolation_murs_plafonds,
    ): ?float;
}
