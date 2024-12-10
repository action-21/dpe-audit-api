<?php

namespace App\Domain\Ventilation\Factory;

use App\Domain\Common\Type\Id;
use App\Domain\Ventilation\Entity\Generateur;
use App\Domain\Ventilation\ValueObject\Signaletique;
use App\Domain\Ventilation\Ventilation;

final class GenerateurFactory
{
    public function build(
        Id $id,
        Ventilation $ventilation,
        string $description,
        Signaletique $signaletique,
        bool $generateur_collectif,
        ?int $annee_installation,
    ): Generateur {
        $entity = new Generateur(
            id: $id,
            ventilation: $ventilation,
            description: $description,
            signaletique: $signaletique,
            generateur_collectif: $signaletique->type->is_generateur_collectif() ?? $generateur_collectif,
            annee_installation: $annee_installation,
        );
        $entity->controle();
        return $entity;
    }
}
