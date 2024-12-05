<?php

namespace App\Domain\Ecs\ValueObject;

use App\Domain\Ecs\Enum\{LabelGenerateur, TypeChaudiere};

class Signaletique
{
    public function __construct(
        public readonly ?TypeChaudiere $type_chaudiere = null,
        public readonly ?LabelGenerateur $label = null,
        public readonly ?bool $presence_ventouse = null,
        public readonly ?float $pn = null,
        public readonly ?float $rpn = null,
        public readonly ?float $qp0 = null,
        public readonly ?float $pveilleuse = null,
        public readonly ?float $cop = null,
    ) {}
}
