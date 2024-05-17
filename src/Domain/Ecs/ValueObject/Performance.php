<?php

namespace App\Domain\Ecs\ValueObject;

final class Performance
{
    public function __construct(
        public readonly ?bool $presence_ventouse = null,
        public readonly ?PuissanceNominale $pn = null,
        public readonly ?Rpn $rpn = null,
        public readonly ?QP0 $qp0 = null,
        public readonly ?PuissanceVeilleuse $pveilleuse = null,
        public readonly ?Cop $cop = null,
    ) {
    }
}
