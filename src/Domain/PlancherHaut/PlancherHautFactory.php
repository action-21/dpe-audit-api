<?php

namespace App\Domain\PlancherHaut;

use App\Domain\Common\Type\Id;
use App\Domain\Enveloppe\Enveloppe;
use App\Domain\PlancherHaut\ValueObject\{Caracteristique, Isolation, Position};

final class PlancherHautFactory
{
    public function build(
        Id $id,
        Enveloppe $enveloppe,
        string $description,
        Position $position,
        Caracteristique $caracteristique,
        Isolation $isolation,
    ): PlancherHaut {
        $entity = new PlancherHaut(
            id: $id,
            enveloppe: $enveloppe,
            description: $description,
            position: $position,
            caracteristique: $caracteristique,
            isolation: $isolation,
        );
        $entity->controle();
        return $entity;
    }
}
