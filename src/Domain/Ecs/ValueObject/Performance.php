<?php

namespace App\Domain\Ecs\ValueObject;

use Webmozart\Assert\Assert;

final class Performance
{
    public function __construct(
        public readonly float $pn,
        public readonly float $paux,
        public readonly ?float $cop,
        public readonly ?float $rpn,
        public readonly ?float $qp0,
        public readonly ?float $pveilleuse,
    ) {}

    public static function create(
        float $pn,
        float $paux,
        ?float $cop,
        ?float $rpn,
        ?float $qp0,
        ?float $pveilleuse,
    ): self {
        Assert::greaterThanEq($pn, 0);
        Assert::greaterThanEq($paux, 0);
        Assert::nullOrGreaterThan($cop, 0);
        Assert::nullOrGreaterThan($rpn, 0);
        Assert::nullOrGreaterThan($qp0, 0);
        Assert::nullOrGreaterThan($pveilleuse, 0);

        return new static(
            pn: $pn,
            paux: $paux,
            cop: $cop,
            rpn: $rpn,
            qp0: $qp0,
            pveilleuse: $pveilleuse,
        );
    }
}
