<?php

namespace App\Domain\Ecs\ValueObject\Signaletique;

use App\Domain\Ecs\Enum\{EnergieGenerateur, TypeGenerateur};
use App\Domain\Ecs\ValueObject\Signaletique;

final class Accumulateur extends Signaletique
{
    public static function create(
        TypeGenerateur\Accumulateur $type,
        EnergieGenerateur\Accumulateur $energie,
        int $volume_stockage,
        bool $position_volume_chauffe,
        bool $generateur_collectif,
        ?bool $presence_ventouse,
        ?float $pn,
        ?float $rpn,
        ?float $qp0,
    ): static {
        $value = new static(
            type: $type->to(),
            energie: $energie->to(),
            volume_stockage: $volume_stockage,
            position_volume_chauffe: $position_volume_chauffe,
            generateur_collectif: $generateur_collectif,
            presence_ventouse: $presence_ventouse,
            pn: $pn,
            rpn: $rpn,
            qp0: $qp0,
        );
        $value->controle();
        return $value;
    }
}
