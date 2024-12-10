<?php

namespace App\Domain\Chauffage\ValueObject;

use Webmozart\Assert\Assert;

final class Performance
{
    public function __construct(
        public readonly float $pn,
        public readonly float $paux,
        public readonly ?float $scop,
        public readonly ?float $rpn,
        public readonly ?float $rpint,
        public readonly ?float $qp0,
        public readonly ?float $pveilleuse,
        public readonly ?float $tfonc30,
        public readonly ?float $tfonc100,
    ) {}

    public static function create(
        float $pn,
        float $paux,
        ?float $scop,
        ?float $rpn,
        ?float $rpint,
        ?float $qp0,
        ?float $pveilleuse,
        ?float $tfonc30,
        ?float $tfonc100,
    ): self {
        Assert::greaterThanEq($pn, 0);
        Assert::greaterThanEq($paux, 0);
        Assert::greaterThanEq($scop, 0);
        Assert::greaterThanEq($rpn, 0);
        Assert::greaterThanEq($rpint, 0);
        Assert::greaterThanEq($qp0, 0);
        Assert::greaterThanEq($pveilleuse, 0);
        Assert::greaterThanEq($tfonc30, 0);
        Assert::greaterThanEq($tfonc100, 0);

        return new static(
            pn: $pn,
            paux: $paux,
            scop: $scop,
            rpn: $rpn,
            rpint: $rpint,
            qp0: $qp0,
            pveilleuse: $pveilleuse,
            tfonc30: $tfonc30,
            tfonc100: $tfonc100,
        );
    }
}
