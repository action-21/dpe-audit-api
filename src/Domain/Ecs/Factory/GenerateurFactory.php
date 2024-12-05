<?php

namespace App\Domain\Ecs\Factory;

use App\Domain\Common\Type\Id;
use App\Domain\Ecs\Ecs;
use App\Domain\Ecs\Entity\Generateur;
use App\Domain\Ecs\ValueObject\Signaletique;

final class GenerateurFactory
{
    public function build(
        Id $id,
        Ecs $ecs,
        string $description,
        ?Id $generateur_mixte_id,
        ?Id $reseau_chaleur_id,
        ?int $annee_installation,
        Signaletique $signaletique,
    ): Generateur {
        $entity = new Generateur(
            id: $id,
            ecs: $ecs,
            description: $description,
            generateur_mixte_id: $generateur_mixte_id,
            reseau_chaleur_id: $reseau_chaleur_id,
            annee_installation: $annee_installation,
            signaletique: $signaletique,
        );
        $entity->controle();
        return $entity;
    }
}
