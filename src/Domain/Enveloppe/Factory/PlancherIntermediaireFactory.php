<?php

namespace App\Domain\Enveloppe\Factory;

use App\Domain\Common\Type\Id;
use App\Domain\Enveloppe\Entity\PlancherIntermediaire;
use App\Domain\Enveloppe\Enum\Inertie\InertiePlancherIntermediaire;
use App\Domain\Enveloppe\Enveloppe;

final class PlancherIntermediaireFactory
{
    public function build(
        Enveloppe $enveloppe,
        string $description,
        float $surface,
        InertiePlancherIntermediaire $inertie,
    ): PlancherIntermediaire {
        $entity = new PlancherIntermediaire(
            id: Id::create(),
            enveloppe: $enveloppe,
            description: $description,
            surface: $surface,
            inertie: $inertie->to(),
        );
        $entity->controle();
        return $entity;
    }
}
