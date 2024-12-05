<?php

namespace App\Domain\Ecs\ValueObject\Signaletique;

use App\Domain\Ecs\ValueObject\Signaletique;
use Webmozart\Assert\Assert;

final class Combustion extends Signaletique
{
    public static function create(
        ?bool $presence_ventouse = null,
        ?float $pn = null,
        ?float $rpn = null,
        ?float $qp0 = null,
    ): static {
        Assert::greaterThan($pn, 0);
        Assert::greaterThan($rpn, 0);
        Assert::greaterThanEq($qp0, 0);

        return new self(
            presence_ventouse: $presence_ventouse,
            pn: $pn,
            rpn: $rpn,
            qp0: $qp0,
        );
    }
}
