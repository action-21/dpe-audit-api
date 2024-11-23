<?php

namespace App\Domain\Refroidissement\Factory;

use App\Domain\Common\Type\Id;
use App\Domain\Refroidissement\Entity\{Installation, SystemeCollection};
use App\Domain\Refroidissement\Refroidissement;

final class InstallationFactory
{
    public function build(
        Id $id,
        Refroidissement $refroidissement,
        string $description,
        float $surface,
    ): Installation {
        $entity = new Installation(
            id: $id,
            refroidissement: $refroidissement,
            description: $description,
            surface: $surface,
            systemes: new SystemeCollection(),
        );
        $entity->controle();
        return $entity;
    }
}
