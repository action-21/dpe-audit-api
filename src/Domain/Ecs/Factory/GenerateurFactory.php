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
        bool $position_volume_chauffe,
        bool $generateur_collectif,
        Signaletique $signaletique,
    ): Generateur {
        $entity = new Generateur(
            id: $id,
            ecs: $ecs,
            description: $description,
            generateur_mixte_id: $signaletique->type->is_usage_mixte() ? $generateur_mixte_id : null,
            reseau_chaleur_id: $signaletique->type->is_reseau_chaleur() ? $reseau_chaleur_id : null,
            annee_installation: $annee_installation,
            position_volume_chauffe: $signaletique->type->position_volume_chauffe() ?? $position_volume_chauffe,
            generateur_collectif: $signaletique->type->is_generateur_collectif() ?? $generateur_collectif,
            signaletique: $signaletique,
        );
        $entity->controle();
        return $entity;
    }
}
