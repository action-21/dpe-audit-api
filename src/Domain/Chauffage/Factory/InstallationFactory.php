<?php

namespace App\Domain\Chauffage\Factory;

use App\Domain\Chauffage\Chauffage;
use App\Domain\Chauffage\Entity\{Installation, SystemeCollection};
use App\Domain\Chauffage\ValueObject\{Regulation, Solaire};
use App\Domain\Common\Type\Id;

final class InstallationFactory
{
    public function build(
        Id $id,
        Chauffage $chauffage,
        string $description,
        float $surface,
        bool $comptage_individuel,
        Solaire $solaire,
        Regulation $regulation_centrale,
        Regulation $regulation_terminale,
    ): Installation {
        $entity = new Installation(
            id: $id,
            chauffage: $chauffage,
            description: $description,
            surface: $surface,
            solaire: $solaire,
            regulation_centrale: $regulation_centrale,
            regulation_terminale: $regulation_terminale,
            comptage_individuel: $comptage_individuel,
            systemes: new SystemeCollection(),
        );
        $entity->controle();
        return $entity;
    }
}
