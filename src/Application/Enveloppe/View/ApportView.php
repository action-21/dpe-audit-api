<?php

namespace App\Application\Enveloppe\View;

use App\Domain\Common\Enum\Mois;
use App\Domain\Enveloppe\EnveloppeEngine;

class ApportView
{
    public function __construct(
        public readonly ?float $apport_ch = null,
        public readonly ?float $apport_ch_depensier = null,
        public readonly ?float $apport_fr = null,
        public readonly ?float $apport_fr_depensier = null,
        public readonly ?float $sse = null,
        public readonly ?float $sse_depensier = null,
        public readonly ?float $apport_interne_occupant = null,
        public readonly ?float $apport_interne_eclairage = null,
        public readonly ?float $apport_interne_equipements = null,
        public readonly ?float $apport_solaire_ch = null,
        public readonly ?float $apport_solaire_ch_depensier = null,
        public readonly ?float $apport_interne_ch = null,
        public readonly ?float $apport_interne_ch_depensier = null,
        public readonly ?float $apport_solaire_fr = null,
        public readonly ?float $apport_solaire_fr_depensier = null,
        public readonly ?float $apport_interne_fr = null,
        public readonly ?float $apport_interne_fr_depensier = null,
        /** @var ?array<float> */
        public readonly ?array $sse_j = null,
        /** @var ?array<float> */
        public readonly ?array $sse_j_depensier = null,
        /** @var ?array<float> */
        public readonly ?array $f_j = null,
        /** @var ?array<float> */
        public readonly ?array $f_j_depensier = null,
        /** @var ?array<float> */
        public readonly ?array $apport_ch_j = null,
        /** @var ?array<float> */
        public readonly ?array $apport_ch_j_depensier = null,
        /** @var ?array<float> */
        public readonly ?array $apport_solaire_ch_j = null,
        /** @var ?array<float> */
        public readonly ?array $apport_solaire_ch_j_depensier = null,
        /** @var ?array<float> */
        public readonly ?array $apport_interne_ch_j = null,
        /** @var ?array<float> */
        public readonly ?array $apport_interne_ch_j_depensier = null,
        /** @var ?array<float> */
        public readonly ?array $apport_fr_j = null,
        /** @var ?array<float> */
        public readonly ?array $apport_fr_j_depensier = null,
        /** @var ?array<float> */
        public readonly ?array $apport_solaire_fr_j = null,
        /** @var ?array<float> */
        public readonly ?array $apport_solaire_fr_j_depensier = null,
        /** @var ?array<float> */
        public readonly ?array $apport_interne_fr_j = null,
        /** @var ?array<float> */
        public readonly ?array $apport_interne_fr_j_depensier = null,
    ) {
    }

    public static function from_engine(EnveloppeEngine $engine): self
    {
        return new self(
            apport_ch: $engine->apport()->apport_ch(),
            apport_ch_depensier: $engine->apport()->apport_ch(scenario_depensier: true),
            apport_fr: $engine->apport()->apport_fr(),
            apport_fr_depensier: $engine->apport()->apport_fr(scenario_depensier: true),
            sse: $engine->apport()->sse(),
            sse_depensier: $engine->apport()->sse(scenario_depensier: true),
            apport_interne_occupant: $engine->apport()->apport_interne_occupant(),
            apport_interne_eclairage: $engine->apport()->apport_interne_eclairage(),
            apport_interne_equipements: $engine->apport()->apport_interne_equipements(),
            apport_solaire_ch: $engine->apport()->apport_solaire_ch(),
            apport_solaire_ch_depensier: $engine->apport()->apport_solaire_ch(scenario_depensier: true),
            apport_interne_ch: $engine->apport()->apport_interne_ch(),
            apport_interne_ch_depensier: $engine->apport()->apport_interne_ch(scenario_depensier: true),
            apport_solaire_fr: $engine->apport()->apport_solaire_fr(),
            apport_solaire_fr_depensier: $engine->apport()->apport_solaire_fr(scenario_depensier: true),
            apport_interne_fr: $engine->apport()->apport_interne_fr(),
            apport_interne_fr_depensier: $engine->apport()->apport_interne_fr(scenario_depensier: true),
            sse_j: \array_map(fn (Mois $mois): float => $engine->apport()->sse_j($mois), Mois::cases()),
            sse_j_depensier: \array_map(fn (Mois $mois): float => $engine->apport()->sse_j($mois, scenario_depensier: true), Mois::cases()),
            f_j: \array_map(fn (Mois $mois): float => $engine->apport()->f_j($mois), Mois::cases()),
            f_j_depensier: \array_map(fn (Mois $mois): float => $engine->apport()->f_j($mois, scenario_depensier: true), Mois::cases()),
            apport_ch_j: \array_map(fn (Mois $mois): float => $engine->apport()->apport_ch_j($mois), Mois::cases()),
            apport_ch_j_depensier: \array_map(fn (Mois $mois): float => $engine->apport()->apport_ch_j($mois, scenario_depensier: true), Mois::cases()),
            apport_solaire_ch_j: \array_map(fn (Mois $mois): float => $engine->apport()->apport_solaire_ch_j($mois), Mois::cases()),
            apport_solaire_ch_j_depensier: \array_map(fn (Mois $mois): float => $engine->apport()->apport_solaire_ch_j($mois, scenario_depensier: true), Mois::cases()),
            apport_interne_ch_j: \array_map(fn (Mois $mois): float => $engine->apport()->apport_interne_ch_j($mois), Mois::cases()),
            apport_interne_ch_j_depensier: \array_map(fn (Mois $mois): float => $engine->apport()->apport_interne_ch_j($mois, scenario_depensier: true), Mois::cases()),
            apport_fr_j: \array_map(fn (Mois $mois): float => $engine->apport()->apport_fr_j($mois), Mois::cases()),
            apport_fr_j_depensier: \array_map(fn (Mois $mois): float => $engine->apport()->apport_fr_j($mois, scenario_depensier: true), Mois::cases()),
            apport_solaire_fr_j: \array_map(fn (Mois $mois): float => $engine->apport()->apport_solaire_fr_j($mois), Mois::cases()),
            apport_solaire_fr_j_depensier: \array_map(fn (Mois $mois): float => $engine->apport()->apport_solaire_fr_j($mois, scenario_depensier: true), Mois::cases()),
            apport_interne_fr_j: \array_map(fn (Mois $mois): float => $engine->apport()->apport_interne_fr_j($mois), Mois::cases()),
            apport_interne_fr_j_depensier: \array_map(fn (Mois $mois): float => $engine->apport()->apport_interne_fr_j($mois, scenario_depensier: true), Mois::cases()),
        );
    }
}
