<?php

namespace App\Domain\Chauffage\Factory;

use App\Domain\Chauffage\Chauffage;
use App\Domain\Chauffage\Entity\Generateur;
use App\Domain\Chauffage\ValueObject\Signaletique;
use App\Domain\Common\Type\Id;

final class GenerateurFactory
{
    public function build(
        Id $id,
        Chauffage $chauffage,
        string $description,
        ?Id $generateur_mixte_id,
        ?Id $reseau_chaleur_id,
        ?int $annee_installation,
        Signaletique $signaletique,
    ): Generateur {
        $entity = new Generateur(
            id: $id,
            chauffage: $chauffage,
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
