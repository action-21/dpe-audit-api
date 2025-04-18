<?php

namespace App\Domain\Ecs\ValueObject\Generateur;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Ecs\Entity\ReseauChaleur;

final class Position
{
    public function __construct(
        public readonly bool $generateur_collectif,
        public readonly bool $generateur_multi_batiment,
        public readonly bool $position_volume_chauffe,
        public readonly ?Id $generateur_mixte_id,
        public readonly ?ReseauChaleur $reseau_chaleur,
    ) {}

    public static function create(
        bool $generateur_collectif,
        bool $position_volume_chauffe,
        bool $generateur_multi_batiment,
        ?Id $generateur_mixte_id = null,
        ?ReseauChaleur $reseau_chaleur = null,
    ): self {
        return new self(
            generateur_collectif: $generateur_multi_batiment ? true : $generateur_collectif,
            position_volume_chauffe: $generateur_multi_batiment ? false : $position_volume_chauffe,
            generateur_multi_batiment: $generateur_multi_batiment,
            generateur_mixte_id: $generateur_mixte_id,
            reseau_chaleur: $reseau_chaleur,
        );
    }
}
