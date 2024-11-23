<?php

namespace App\Domain\Chauffage\ValueObject\Signaletique;

use App\Domain\Chauffage\Enum\TypeChaudiere;
use App\Domain\Chauffage\ValueObject\Signaletique;

final class PacHybride extends Signaletique
{
    public static function create(
        TypeChaudiere $type_chaudiere,
        ?int $priorite_cascade,
        ?bool $presence_regulation_combustion,
        ?bool $presence_ventouse,
        ?float $pn,
        ?float $rpn,
        ?float $rpint,
        ?float $qp0,
        ?float $pveilleuse,
        ?float $tfonc30,
        ?float $tfonc100,
        ?float $scop,
    ): static {
        return new self(
            type_chaudiere: $type_chaudiere,
            priorite_cascade: $priorite_cascade,
            presence_regulation_combustion: $presence_regulation_combustion,
            presence_ventouse: $presence_ventouse,
            pn: $pn,
            rpn: $rpn,
            rpint: $rpint,
            qp0: $qp0,
            pveilleuse: $pveilleuse,
            tfonc30: $tfonc30,
            tfonc100: $tfonc100,
            scop: $scop,
        );
    }
}
