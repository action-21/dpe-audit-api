<?php

namespace App\Domain\Chauffage\Factory;

use App\Domain\Chauffage\Chauffage;
use App\Domain\Chauffage\Entity\Emetteur;
use App\Domain\Chauffage\Enum\{TemperatureDistribution, TypeEmetteur};
use App\Domain\Common\Type\Id;

final class EmetteurFactory
{
    public function build(
        Id $id,
        Chauffage $chauffage,
        string $description,
        TypeEmetteur $type,
        TemperatureDistribution $temperature_distribution,
        bool $presence_robinet_thermostatique,
        ?int $annee_installation,
    ): Emetteur {
        $entity = new Emetteur(
            id: $id,
            chauffage: $chauffage,
            description: $description,
            type: $type,
            temperature_distribution: $temperature_distribution,
            presence_robinet_thermostatique: $presence_robinet_thermostatique,
            annee_installation: $annee_installation,
        );
        $entity->controle();
        return $entity;
    }
}
