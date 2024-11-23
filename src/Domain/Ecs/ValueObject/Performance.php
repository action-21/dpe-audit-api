<?php

namespace App\Domain\Ecs\ValueObject;

use App\Domain\Common\Service\Assert;

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
        Assert::positif_ou_zero($pn);
        Assert::positif_ou_zero($paux);
        Assert::positif_ou_zero($cop);
        Assert::positif_ou_zero($rpn);
        Assert::positif_ou_zero($qp0);
        Assert::positif_ou_zero($pveilleuse);

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
