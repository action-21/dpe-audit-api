<?php

namespace App\Domain\Ecs\ValueObject\Signaletique;

use App\Domain\Ecs\Enum\TypeChaudiere;
use App\Domain\Ecs\ValueObject\Signaletique;
use Webmozart\Assert\Assert;

final class Chaudiere extends Signaletique
{
    public static function create(
        TypeChaudiere $type_chaudiere,
        ?bool $presence_ventouse,
        ?float $pn,
        ?float $rpn,
        ?float $qp0,
        ?float $pveilleuse,
    ): static {
        Assert::greaterThan($pn, 0);
        Assert::greaterThan($rpn, 0);
        Assert::greaterThanEq($qp0, 0);
        Assert::greaterThanEq($pveilleuse, 0);

        return new self(
            type_chaudiere: $type_chaudiere,
            presence_ventouse: $presence_ventouse,
            pn: $pn,
            rpn: $rpn,
            qp0: $qp0,
            pveilleuse: $pveilleuse,
        );
    }
}
