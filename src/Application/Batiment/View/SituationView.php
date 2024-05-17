<?php

namespace App\Application\Batiment\View;

use App\Domain\Batiment\BatimentEngine;
use App\Domain\Common\Enum\Mois;

class SituationView
{
    public function __construct(
        public readonly null|float $tbase = null,
        /** @var ?array<null|float> */
        public readonly ?array $epv_j = null,
        /** @var ?array<null|float> */
        public readonly ?array $e_j = null,
        /** @var ?array<null|float> */
        public readonly ?array $e_fr_j = null,
        /** @var ?array<null|float> */
        public readonly ?array $e_fr_j_depensier = null,
        /** @var ?array<null|float> */
        public readonly ?array $dh_ch_j = null,
        /** @var ?array<null|float> */
        public readonly ?array $dh_ch_j_depensier = null,
        /** @var ?array<null|float> */
        public readonly ?array $nref_ch_j = null,
        /** @var ?array<null|float> */
        public readonly ?array $nref_ch_j_depensier = null,
        /** @var ?array<null|float> */
        public readonly ?array $nref_fr_j = null,
        /** @var ?array<null|float> */
        public readonly ?array $nref_fr_j_depensier = null,
        /** @var ?array<null|float> */
        public readonly ?array $text_moy_clim_j = null,
        /** @var ?array<null|float> */
        public readonly ?array $text_moy_clim_j_depensier = null,
        /** @var ?array<null|float> */
        public readonly ?array $tefs_j = null,
    ) {
    }

    public static function from_engine(BatimentEngine $engine): self
    {
        return new self(
            tbase: $engine->situation()->tbase(),
            epv_j: \array_map(fn (Mois $mois): null|float => $engine->situation()->epv_j($mois), Mois::cases()),
            e_j: \array_map(fn (Mois $mois): null|float => $engine->situation()->e_j($mois), Mois::cases()),
            e_fr_j: \array_map(fn (Mois $mois): null|float => $engine->situation()->e_fr_j($mois), Mois::cases()),
            e_fr_j_depensier: \array_map(fn (Mois $mois): null|float => $engine->situation()->e_fr_j($mois, true), Mois::cases()),
            dh_ch_j: \array_map(fn (Mois $mois): null|float => $engine->situation()->dh_ch_j($mois), Mois::cases()),
            dh_ch_j_depensier: \array_map(fn (Mois $mois): null|float => $engine->situation()->dh_ch_j($mois, true), Mois::cases()),
            nref_ch_j: \array_map(fn (Mois $mois): null|float => $engine->situation()->nref_ch_j($mois), Mois::cases()),
            nref_ch_j_depensier: \array_map(fn (Mois $mois): null|float => $engine->situation()->nref_ch_j($mois, true), Mois::cases()),
            nref_fr_j: \array_map(fn (Mois $mois): null|float => $engine->situation()->nref_fr_j($mois), Mois::cases()),
            nref_fr_j_depensier: \array_map(fn (Mois $mois): null|float => $engine->situation()->nref_fr_j($mois, true), Mois::cases()),
            text_moy_clim_j: \array_map(fn (Mois $mois): null|float => $engine->situation()->text_moy_clim_j($mois), Mois::cases()),
            text_moy_clim_j_depensier: \array_map(fn (Mois $mois): null|float => $engine->situation()->text_moy_clim_j($mois, true), Mois::cases()),
            tefs_j: \array_map(fn (Mois $mois): null|float => $engine->situation()->tefs_j($mois), Mois::cases()),
        );
    }
}
