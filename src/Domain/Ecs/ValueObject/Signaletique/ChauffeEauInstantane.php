<?php

namespace App\Domain\Ecs\ValueObject\Signaletique;

use App\Domain\Ecs\Enum\{EnergieGenerateur, TypeGenerateur};
use App\Domain\Ecs\ValueObject\Signaletique;

final class ChauffeEauInstantane extends Signaletique
{
    public static function create(
        EnergieGenerateur\ChauffeEauInstantane $energie,
        bool $position_volume_chauffe,
        bool $generateur_collectif,
        ?float $pn,
        ?float $rpn,
        ?float $qp0,
    ): static {
        $value = new static(
            type: TypeGenerateur::CHAUFFE_EAU_INSTANTANE,
            energie: $energie->to(),
            volume_stockage: 0,
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
