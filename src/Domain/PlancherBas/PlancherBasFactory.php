<?php

namespace App\Domain\PlancherBas;

use App\Domain\Common\Type\Id;
use App\Domain\Enveloppe\Enveloppe;
use App\Domain\PlancherBas\ValueObject\{Caracteristique, Isolation, Position};

final class PlancherBasFactory
{
    public function build(
        Id $id,
        Enveloppe $enveloppe,
        string $description,
        Position $position,
        Caracteristique $caracteristique,
        Isolation $isolation,
    ): PlancherBas {
        $entity = new PlancherBas(
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
