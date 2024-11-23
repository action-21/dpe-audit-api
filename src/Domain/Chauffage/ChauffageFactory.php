<?php

namespace App\Domain\Chauffage;

use App\Domain\Audit\Audit;
use App\Domain\Chauffage\Entity\{EmetteurCollection, GenerateurCollection, InstallationCollection};

final class ChauffageFactory
{
    public function build(Audit $audit): Chauffage
    {
        return new Chauffage(
            audit: $audit,
            generateurs: new GenerateurCollection(),
            emetteurs: new EmetteurCollection(),
            installations: new InstallationCollection(),
        );
    }
}
