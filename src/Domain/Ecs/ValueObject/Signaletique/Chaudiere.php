<?php

namespace App\Domain\Ecs\ValueObject\Signaletique;

use App\Domain\Ecs\Enum\{EnergieGenerateur, PositionChaudiere, TypeGenerateur};
use App\Domain\Ecs\ValueObject\{Combustion, Signaletique};

final class Chaudiere extends Signaletique
{
    public static function create(
        TypeGenerateur\Chaudiere $type,
        EnergieGenerateur\Chaudiere $energie,
        int $volume_stockage,
        bool $position_volume_chauffe,
        bool $generateur_collectif,
        ?float $pn,
        ?PositionChaudiere $position,
        ?Combustion $combustion,
    ): static {
        $position = $position ?? PositionChaudiere::CHAUDIERE_SOL;

        if ($type->to() === TypeGenerateur::CHAUDIERE_MULTI_BATIMENT) {
            $position_volume_chauffe = false;
            $generateur_collectif = true;
        }
        if ($energie->to() === EnergieGenerateur::ELECTRICITE) {
            $combustion = null;
        }
        if ($energie->to() !== EnergieGenerateur::ELECTRICITE) {
            $combustion = $combustion ?? Combustion::default();
        }
        return new self(
            type: $type->to(),
            energie: $energie->to(),
            volume_stockage: $volume_stockage,
            position_volume_chauffe: $position_volume_chauffe,
            generateur_collectif: $generateur_collectif,
            position_chaudiere: $position,
            pn: $pn,
            combustion: $combustion,
        );
    }
}
