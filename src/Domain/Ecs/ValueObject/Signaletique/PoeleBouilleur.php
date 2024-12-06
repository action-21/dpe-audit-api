<?php

namespace App\Domain\Ecs\ValueObject\Signaletique;

use App\Domain\Ecs\Enum\{EnergieGenerateur, TypeGenerateur};
use App\Domain\Ecs\ValueObject\{Combustion, Signaletique};

final class PoeleBouilleur extends Signaletique
{
    public static function create(
        TypeGenerateur\PoeleBouilleur $type,
        EnergieGenerateur\PoeleBouilleur $energie,
        int $volume_stockage,
        bool $position_volume_chauffe,
        bool $generateur_collectif,
        ?float $pn,
        ?Combustion $combustion,
    ): static {
        $value = new static(
            type: $type->to(),
            energie: $energie->to(),
            volume_stockage: $volume_stockage,
            position_volume_chauffe: $position_volume_chauffe,
            generateur_collectif: $generateur_collectif,
            pn: $pn,
            combustion: $combustion ?? Combustion::default(),
        );
        $value->controle();
        return $value;
    }
}
