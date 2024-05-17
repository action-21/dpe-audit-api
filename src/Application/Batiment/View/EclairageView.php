<?php

namespace App\Application\Batiment\View;

use App\Domain\Batiment\BatimentEngine;
use App\Domain\Common\Enum\Mois;

class EclairageView
{
    public function __construct(
        public readonly null|float $cecl = null,
        public readonly null|float $becl = null,
        /** @var ?array<float> */
        public readonly ?array $cecl_j = null,
        /** @var ?array<float> */
        public readonly ?array $becl_j = null,
    ) {
    }

    public static function from_engine(BatimentEngine $engine): self
    {
        return new self(
            cecl: $engine->eclairage()->cecl(),
            becl: $engine->eclairage()->becl(),
            cecl_j: \array_map(fn (Mois $mois): float => $engine->eclairage()->cecl_j($mois), Mois::cases()),
            becl_j: \array_map(fn (Mois $mois): float => $engine->eclairage()->becl_j($mois), Mois::cases()),
        );
    }
}
