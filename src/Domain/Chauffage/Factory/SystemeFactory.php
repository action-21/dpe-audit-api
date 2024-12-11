<?php

namespace App\Domain\Chauffage\Factory;

use App\Domain\Chauffage\Entity\{EmetteurCollection, Generateur, Installation, Systeme};
use App\Domain\Chauffage\ValueObject\Reseau;
use App\Domain\Common\Type\Id;

final class SystemeFactory
{
    public function build(
        Id $id,
        Installation $installation,
        Generateur $generateur,
        ?Reseau $reseau,
    ): Systeme {
        $entity = new Systeme(
            id: $id,
            installation: $installation,
            generateur: $generateur,
            reseau: $generateur->type()->is_chauffage_central() ? $reseau : null,
            emetteurs: new EmetteurCollection(),
        );
        $entity->controle();
        return $entity;
    }
}
