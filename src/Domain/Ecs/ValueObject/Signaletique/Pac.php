<?php

namespace App\Domain\Ecs\ValueObject\Signaletique;

use App\Domain\Ecs\Enum\{EnergieGenerateur, TypeGenerateur};
use App\Domain\Ecs\ValueObject\Signaletique;

final class Pac extends Signaletique
{
    public static function create(
        TypeGenerateur\Pac $type,
        int $volume_stockage,
        bool $position_volume_chauffe,
        bool $generateur_collectif,
        ?float $pn,
        ?float $cop,
    ): static {
        if ($type->to() === TypeGenerateur::PAC_MULTI_BATIMENT) {
            $position_volume_chauffe = false;
            $generateur_collectif = true;
        }
        $value = new static(
            type: $type->to(),
            energie: EnergieGenerateur::ELECTRICITE,
            volume_stockage: $volume_stockage,
            position_volume_chauffe: $position_volume_chauffe,
            generateur_collectif: $generateur_collectif,
            pn: $pn,
            cop: $cop,
        );
        $value->controle();
        return $value;
    }
}
