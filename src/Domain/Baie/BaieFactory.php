<?php

namespace App\Domain\Baie;

use App\Domain\Baie\Entity\{MasqueLointainCollection, MasqueProcheCollection};
use App\Domain\Baie\ValueObject\{Caracteristique, DoubleFenetre, Position};
use App\Domain\Common\Type\Id;
use App\Domain\Enveloppe\Enveloppe;

final class BaieFactory
{
    public function build(
        Id $id,
        Enveloppe $enveloppe,
        string $description,
        Position $position,
        Caracteristique $caracteristique,
        ?DoubleFenetre $double_fenetre,
    ): Baie {
        $entity = new Baie(
            id: $id,
            enveloppe: $enveloppe,
            description: $description,
            position: $position,
            caracteristique: $caracteristique,
            double_fenetre: $double_fenetre,
            masques_proches: new MasqueProcheCollection(),
            masques_lointains: new MasqueLointainCollection()
        );

        $entity->controle();
        return $entity;
    }
}
