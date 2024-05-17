<?php

namespace App\Domain\Chauffage\ValueObject;

final class Performance
{
    public function __construct(
        public readonly ?bool $presence_regulation_combustion = null,
        public readonly ?bool $presence_ventouse = null,
        public readonly ?PuissanceNominale $pn = null,
        public readonly ?Rpn $rpn = null,
        public readonly ?Rpint $rpint = null,
        public readonly ?QP0 $qp0 = null,
        public readonly ?PuissanceVeilleuse $pveilleuse = null,
        public readonly ?Scop $scop = null,
    ) {
    }
}
