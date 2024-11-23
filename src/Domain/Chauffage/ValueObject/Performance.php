<?php

namespace App\Domain\Chauffage\ValueObject;

use App\Domain\Common\Service\Assert;

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
        Assert::positif_ou_zero($pn);
        Assert::positif_ou_zero($paux);
        Assert::positif_ou_zero($scop);
        Assert::positif_ou_zero($rpn);
        Assert::positif_ou_zero($rpint);
        Assert::positif_ou_zero($qp0);
        Assert::positif_ou_zero($pveilleuse);
        Assert::positif_ou_zero($tfonc30);
        Assert::positif_ou_zero($tfonc100);

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
