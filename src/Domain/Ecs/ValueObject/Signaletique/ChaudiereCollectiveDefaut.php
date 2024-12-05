<?php

namespace App\Domain\Ecs\ValueObject\Signaletique;

use App\Domain\Ecs\Enum\{EnergieGenerateur, TypeGenerateur, TypeChaudiere};
use App\Domain\Ecs\ValueObject\Signaletique;

final class ChaudiereCollectiveDefaut extends Signaletique
{
    public static function create(): static
    {
        return new self(
            type: TypeGenerateur::SYSTEME_COLLECTIF_PAR_DEFAUT,
            energie: EnergieGenerateur::FIOUL,
            volume_stockage: 0,
            position_volume_chauffe: false,
            generateur_collectif: true,
            type_chaudiere: TypeChaudiere::CHAUDIERE_SOL,
        );
    }
}
