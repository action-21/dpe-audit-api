<?php

namespace App\Domain\Chauffage\ValueObject\Generateur;

use App\Domain\Chauffage\Entity\ReseauChaleur;
use App\Domain\Common\ValueObject\Id;

final class Position
{
    public function __construct(
        public readonly bool $position_volume_chauffe,
        public readonly bool $generateur_collectif,
        public readonly bool $generateur_multi_batiment,
        public readonly ?Id $generateur_mixte_id,
        public readonly ?ReseauChaleur $reseau_chaleur,
    ) {}

    public static function create(
        bool $position_volume_chauffe,
        bool $generateur_collectif,
        bool $generateur_multi_batiment,
        ?Id $generateur_mixte_id = null,
        ?ReseauChaleur $reseau_chaleur = null,
    ): self {
        return new self(
            position_volume_chauffe: $generateur_multi_batiment ? false : $position_volume_chauffe,
            generateur_collectif: $generateur_multi_batiment ? true : $generateur_collectif,
            generateur_multi_batiment: $generateur_multi_batiment,
            generateur_mixte_id: $generateur_mixte_id,
            reseau_chaleur: $reseau_chaleur,
        );
    }

    public function with(
        ?bool $position_volume_chauffe = null,
        ?bool $generateur_collectif = null,
        ?bool $generateur_multi_batiment = null,
        ?Id $generateur_mixte_id = null,
        ?ReseauChaleur $reseau_chaleur = null,
    ): self {
        return self::create(
            position_volume_chauffe: $position_volume_chauffe ?? $this->position_volume_chauffe,
            generateur_collectif: $generateur_collectif ?? $this->generateur_collectif,
            generateur_multi_batiment: $generateur_multi_batiment ?? $this->generateur_multi_batiment,
            generateur_mixte_id: $generateur_mixte_id ?? $this->generateur_mixte_id,
            reseau_chaleur: $reseau_chaleur ?? $this->reseau_chaleur,
        );
    }
}
