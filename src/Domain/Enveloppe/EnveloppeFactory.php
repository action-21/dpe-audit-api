<?php

namespace App\Domain\Enveloppe;

use App\Domain\Audit\Audit;
use App\Domain\Enveloppe\Entity\{Parois, PlancherIntermediaireCollection, RefendCollection};
use App\Domain\Enveloppe\Enum\Exposition;
use App\Domain\Lnc\LncCollection;
use App\Domain\PontThermique\PontThermiqueCollection;

final class EnveloppeFactory
{
    public function build(Audit $audit, Exposition $exposition, ?float $q4pa_conv,): Enveloppe
    {
        $entity = new Enveloppe(
            audit: $audit,
            exposition: $exposition,
            q4pa_conv: $q4pa_conv,
            locaux_non_chauffes: new LncCollection(),
            parois: Parois::create(),
            ponts_thermiques: new PontThermiqueCollection(),
            refends: new RefendCollection(),
            planchers_intermediaires: new PlancherIntermediaireCollection(),
        );
        $entity->controle();
        return $entity;
    }
}
