<?php

namespace App\Domain\Ecs\ValueObject\Signaletique;

use App\Domain\Ecs\Enum\{EnergieGenerateur, TypeGenerateur};
use App\Domain\Ecs\ValueObject\Signaletique;

final class ReseauChaleur extends Signaletique
{
    public static function create(TypeGenerateur\ReseauChaleur $type): static
    {
        $value = new static(
            type: $type->to(),
            energie: EnergieGenerateur::RESEAU_CHALEUR,
            volume_stockage: 0,
            position_volume_chauffe: false,
            generateur_collectif: true,
        );
        $value->controle();
        return $value;
    }
}
