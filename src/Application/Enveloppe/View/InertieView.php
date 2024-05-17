<?php

namespace App\Application\Enveloppe\View;

use App\Domain\Common\Enum\Enum;
use App\Domain\Enveloppe\EnveloppeEngine;

class InertieView
{
    public function __construct(
        public readonly ?Enum $inertie = null,
        public readonly ?bool $paroi_verticale_lourde = null,
        public readonly ?bool $plancher_bas_lourd = null,
        public readonly ?bool $plancher_haut_lourd = null,
    ) {
    }

    public static function from_engine(EnveloppeEngine $engine): self
    {
        return new self(
            inertie: $engine->inertie()->classe_inertie(),
            paroi_verticale_lourde: $engine->inertie()->paroi_verticale_lourde(),
            plancher_bas_lourd: $engine->inertie()->plancher_bas_lourd(),
            plancher_haut_lourd: $engine->inertie()->plancher_haut_lourd(),
        );
    }
}
