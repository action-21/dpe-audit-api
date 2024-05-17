<?php

namespace App\Application\Batiment\View;

use App\Domain\Batiment\BatimentEngine;
use App\Domain\Common\Enum\Mois;

class RefroidissementView
{
    public function __construct(
        public readonly ?float $cfr = null,
        public readonly ?float $cfr_depensier = null,
        public readonly ?float $bfr = null,
        public readonly ?float $bfr_depensier = null,
        public readonly ?float $tint = null,
        public readonly ?float $tint_depensier = null,
        public readonly ?float $t = null,
        public readonly ?float $cin = null,
        /** @var ?array<float> */
        public readonly ?array $cfr_j = null,
        /** @var ?array<float> */
        public readonly ?array $cfr_j_depensier = null,
        /** @var ?array<float> */
        public readonly ?array $bfr_j = null,
        /** @var ?array<float> */
        public readonly ?array $bfr_j_depensier = null,
        /** @var ?array<null|float> */
        public readonly ?array $fut_j = null,
        /** @var ?array<null|float> */
        public readonly ?array $fut_j_depensier = null,
        /** @var ?array<null|float> */
        public readonly ?array $rbth_j = null,
        /** @var ?array<null|float> */
        public readonly ?array $rbth_j_depensier = null,
    ) {
    }

    public static function from_engine(BatimentEngine $engine): self
    {
        return new self(
            cfr: $engine->refroidissement()->cfr(),
            cfr_depensier: $engine->refroidissement()->cfr(scenario_depensier: true),
            bfr: $engine->refroidissement()->bfr(),
            bfr_depensier: $engine->refroidissement()->bfr(scenario_depensier: true),
            tint: $engine->refroidissement()->tint(),
            tint_depensier: $engine->refroidissement()->tint(scenario_depensier: true),
            t: $engine->refroidissement()->t(),
            cin: $engine->refroidissement()->cin(),
            cfr_j: \array_map(fn (Mois $mois): float => $engine->refroidissement()->cfr_j($mois), Mois::cases()),
            cfr_j_depensier: \array_map(fn (Mois $mois): float => $engine->refroidissement()->cfr_j($mois, true), Mois::cases()),
            bfr_j: \array_map(fn (Mois $mois): float => $engine->refroidissement()->bfr_j($mois), Mois::cases()),
            bfr_j_depensier: \array_map(fn (Mois $mois): float => $engine->refroidissement()->bfr_j($mois, true), Mois::cases()),
            fut_j: \array_map(fn (Mois $mois): ?float => $engine->refroidissement()->fut_j($mois), Mois::cases()),
            fut_j_depensier: \array_map(fn (Mois $mois): ?float => $engine->refroidissement()->fut_j($mois, true), Mois::cases()),
            rbth_j: \array_map(fn (Mois $mois): ?float => $engine->refroidissement()->rbth_j($mois), Mois::cases()),
            rbth_j_depensier: \array_map(fn (Mois $mois): ?float => $engine->refroidissement()->rbth_j($mois, true), Mois::cases()),
        );
    }
}
