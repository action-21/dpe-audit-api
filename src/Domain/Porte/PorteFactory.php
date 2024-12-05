<?php

namespace App\Domain\Porte;

use App\Domain\Common\Type\Id;
use App\Domain\Enveloppe\Enveloppe;
use App\Domain\Porte\ValueObject\{Caracteristique, Position};

final class PorteFactory
{
    public function build(
        Id $id,
        Enveloppe $enveloppe,
        string $description,
        Position $position,
        Caracteristique $caracteristique,
    ): Porte {
        $entity = new Porte(
            id: $id,
            enveloppe: $enveloppe,
            description: $description,
            position: $position,
            caracteristique: $caracteristique,
        );
        $entity->controle();
        return $entity;
    }
}
