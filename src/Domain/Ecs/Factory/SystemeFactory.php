<?php

namespace App\Domain\Ecs\Factory;

use App\Domain\Common\Type\Id;
use App\Domain\Ecs\Entity\{Generateur, Installation, Systeme};
use App\Domain\Ecs\ValueObject\{Reseau, Stockage};

final class SystemeFactory
{
    public function build(
        Id $id,
        Installation $installation,
        Generateur $generateur,
        Reseau $reseau,
        ?Stockage $stockage,
    ): Systeme {
        $entity = new Systeme(
            id: $id,
            installation: $installation,
            generateur: $generateur,
            reseau: $reseau,
            stockage: $stockage,
        );

        $entity->controle();
        return $entity;
    }
}
