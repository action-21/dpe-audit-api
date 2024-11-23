<?php

namespace App\Domain\Visite\Factory;

use App\Domain\Common\Type\Id;
use App\Domain\Visite\Entity\Logement;
use App\Domain\Visite\Enum\Typologie;
use App\Domain\Visite\Visite;

final class LogementFactory
{
    public function build(
        Id $id,
        Visite $visite,
        string $description,
        Typologie $typologie,
        float $surface_habitable,
    ): Logement {
        $entity = new Logement(
            id: $id,
            visite: $visite,
            description: $description,
            typologie: $typologie,
            surface_habitable: $surface_habitable,
        );

        $entity->controle();
        return $entity;
    }
}
