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
        bool $position_volume_chauffe,
        bool $generateur_collectif,
        Signaletique $signaletique,
    ): Generateur {
        $entity = new Generateur(
            id: $id,
            chauffage: $chauffage,
            description: $description,
            generateur_mixte_id: $signaletique->type->is_usage_mixte() ? $generateur_mixte_id : null,
            reseau_chaleur_id: $signaletique->type->is_reseau_chaleur() ? $reseau_chaleur_id : null,
            position_volume_chauffe: $signaletique->type->position_volume_chauffe() ?? $position_volume_chauffe,
            generateur_collectif: $signaletique->type->is_generateur_collectif() ?? $generateur_collectif,
            annee_installation: $annee_installation,
            signaletique: $signaletique,
        );
        $entity->controle();
        return $entity;
    }
}
