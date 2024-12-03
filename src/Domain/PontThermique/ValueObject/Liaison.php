<?php

namespace App\Domain\PontThermique\ValueObject;

use App\Domain\Common\Type\Id;
use App\Domain\PontThermique\Enum\TypeLiaison;

final class Liaison
{
    public function __construct(
        public readonly TypeLiaison $type,
        public readonly bool $pont_thermique_partiel,
        public readonly Id $mur_id,
        public readonly ?Id $plancher_id = null,
        public readonly ?Id $ouverture_id = null,
    ) {}

    public static function create_liaison_plancher_bas_mur(Id $mur_id, Id $plancher_id,): self
    {
        return new self(
            type: TypeLiaison::PLANCHER_BAS_MUR,
            mur_id: $mur_id,
            plancher_id: $plancher_id,
            pont_thermique_partiel: false,
        );
    }

    public static function create_liaison_plancher_intermediaire_mur(Id $mur_id, bool $pont_thermique_partiel): self
    {
        return new self(
            type: TypeLiaison::PLANCHER_INTERMEDIAIRE_MUR,
            mur_id: $mur_id,
            pont_thermique_partiel: $pont_thermique_partiel,
        );
    }

    public static function create_liaison_plancher_haut_mur(Id $mur_id, Id $plancher_id,): self
    {
        return new self(
            type: TypeLiaison::PLANCHER_HAUT_MUR,
            mur_id: $mur_id,
            plancher_id: $plancher_id,
            pont_thermique_partiel: false,
        );
    }

    public static function create_liaison_refend_mur(Id $mur_id, bool $pont_thermique_partiel,): self
    {
        return new self(
            type: TypeLiaison::REFEND_MUR,
            mur_id: $mur_id,
            pont_thermique_partiel: $pont_thermique_partiel,
        );
    }

    public static function create_liaison_menuiserie_mur(Id $mur_id, Id $ouverture_id): self
    {
        return new self(
            type: TypeLiaison::MENUISERIE_MUR,
            mur_id: $mur_id,
            ouverture_id: $ouverture_id,
            pont_thermique_partiel: false,
        );
    }
}
