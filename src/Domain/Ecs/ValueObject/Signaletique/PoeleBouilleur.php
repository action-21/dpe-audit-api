<?php

namespace App\Domain\Ecs\ValueObject\Signaletique;

use App\Domain\Ecs\Enum\{EnergieGenerateur, TypeGenerateur};
use App\Domain\Ecs\ValueObject\Signaletique;

final class PoeleBouilleur extends Signaletique
{
    public static function create(
        EnergieGenerateur\PoeleBouilleur $energie,
        int $volume_stockage,
        bool $position_volume_chauffe,
        bool $generateur_collectif,
        ?float $pn,
        ?float $rpn,
        ?float $qp0,
    ): static {
        $value = new static(
            type: TypeGenerateur::POELE_BOUILLEUR,
            energie: $energie->to(),
            volume_stockage: $volume_stockage,
            position_volume_chauffe: $position_volume_chauffe,
            generateur_collectif: $generateur_collectif,
            pn: $pn,
            rpn: $rpn,
            qp0: $qp0,
        );
        $value->controle();
        return $value;
    }
}
