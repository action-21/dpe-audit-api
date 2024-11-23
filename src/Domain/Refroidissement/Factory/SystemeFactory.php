<?php

namespace App\Domain\Refroidissement\Factory;

use App\Domain\Common\Type\Id;
use App\Domain\Refroidissement\Entity\{Generateur, Installation, Systeme};

final class SystemeFactory
{
    public function build(
        Id $id,
        Installation $installation,
        Generateur $generateur,
    ): Systeme {
        return new Systeme(
            id: $id,
            installation: $installation,
            generateur: $generateur,
        );
    }
}
