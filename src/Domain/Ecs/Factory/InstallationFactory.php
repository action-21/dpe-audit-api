<?php

namespace App\Domain\Ecs\Factory;

use App\Domain\Common\Type\Id;
use App\Domain\Ecs\Ecs;
use App\Domain\Ecs\Entity\{Installation, SystemeCollection};
use App\Domain\Ecs\ValueObject\Solaire;

final class InstallationFactory
{
    public function build(
        Id $id,
        Ecs $ecs,
        string $description,
        float $surface,
        ?Solaire $solaire,
    ): Installation {
        $entity = new Installation(
            id: $id,
            ecs: $ecs,
            description: $description,
            surface: $surface,
            solaire: $solaire,
            systemes: new SystemeCollection(),
        );
        $entity->controle();
        return $entity;
    }
}
