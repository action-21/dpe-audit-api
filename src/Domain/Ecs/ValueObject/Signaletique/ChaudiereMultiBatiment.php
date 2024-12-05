<?php

namespace App\Domain\Ecs\ValueObject\Signaletique;

use App\Domain\Ecs\Enum\{EnergieGenerateur, TypeGenerateur, TypeChaudiere};
use App\Domain\Ecs\ValueObject\Signaletique;

final class ChaudiereMultiBatiment extends Signaletique
{
    public static function create(
        EnergieGenerateur\ChaudiereMultiBatiment $energie,
        int $volume_stockage,
        bool $position_volume_chauffe,
        TypeChaudiere $type_chaudiere,
    ): static {
        return new self(
            type: TypeGenerateur::CHAUDIERE_MULTI_BATIMENT,
            energie: $energie->to(),
            volume_stockage: $volume_stockage,
            position_volume_chauffe: $position_volume_chauffe,
            generateur_collectif: true,
            type_chaudiere: $type_chaudiere,
        );
    }
}
