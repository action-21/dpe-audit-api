<?php

namespace App\Domain\Refroidissement;

use App\Domain\Audit\Audit;
use App\Domain\Refroidissement\Entity\{GenerateurCollection, InstallationCollection};

final class RefroidissementFactory
{
    public function build(Audit $audit): Refroidissement
    {
        return new Refroidissement(
            audit: $audit,
            generateurs: new GenerateurCollection(),
            installations: new InstallationCollection(),
        );
    }
}
