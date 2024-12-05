<?php

namespace App\Domain\Ecs\ValueObject\Signaletique;

use App\Domain\Ecs\Enum\{EnergieGenerateur, TypeGenerateur, TypeChaudiere};
use App\Domain\Ecs\ValueObject\Signaletique;

final class PacMultiBatiment extends Signaletique
{
    public static function create(
        int $volume_stockage,
        bool $position_volume_chauffe,
        TypeChaudiere $type_chaudiere,
    ): static {
        return new self(
            type: TypeGenerateur::PAC_MULTI_BATIMENT,
            energie: EnergieGenerateur::ELECTRICITE,
            volume_stockage: $volume_stockage,
            position_volume_chauffe: $position_volume_chauffe,
            generateur_collectif: true,
            type_chaudiere: $type_chaudiere,
        );
    }
}
