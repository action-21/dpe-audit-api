<?php

namespace App\Domain\Mur;

use App\Domain\Common\Type\Id;
use App\Domain\Enveloppe\Enveloppe;
use App\Domain\Mur\ValueObject\{Caracteristique, Isolation, Position};

final class MurFactory
{
    public function build(
        Id $id,
        Enveloppe $enveloppe,
        string $description,
        Position $position,
        Caracteristique $caracteristique,
        Isolation $isolation,
    ): Mur {
        $entity = new Mur(
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
