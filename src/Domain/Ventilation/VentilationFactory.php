<?php

namespace App\Domain\Ventilation;

use App\Domain\Audit\Audit;
use App\Domain\Ventilation\Entity\{GenerateurCollection, InstallationCollection};

final class VentilationFactory
{
    public function build(Audit $audit): Ventilation
    {
        return new Ventilation(
            audit: $audit,
            generateurs: new GenerateurCollection(),
            installations: new InstallationCollection(),
        );
    }
}
