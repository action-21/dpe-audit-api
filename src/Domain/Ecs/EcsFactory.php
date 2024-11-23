<?php

namespace App\Domain\Ecs;

use App\Domain\Audit\Audit;
use App\Domain\Ecs\Entity\{GenerateurCollection, InstallationCollection};

final class EcsFactory
{
    public function build(Audit $audit): Ecs
    {
        return new Ecs(
            audit: $audit,
            generateurs: new GenerateurCollection(),
            installations: new InstallationCollection(),
        );
    }
}
