<?php

namespace App\Domain\Audit\ValueObject;

use App\Domain\Common\Enum\Mois;

final class SollicitationExterieure
{
    public function __construct(
        public readonly Mois $mois,
        public readonly float $epv,
        public readonly float $e,
        public readonly float $efr26,
        public readonly float $efr28,
        public readonly float $nref19,
        public readonly float $nref21,
        public readonly float $nref26,
        public readonly float $nref28,
        public readonly float $dh14,
        public readonly float $dh19,
        public readonly float $dh21,
        public readonly float $dh26,
        public readonly float $dh28,
        public readonly ?float $tefs,
        public readonly ?float $text,
        public readonly ?float $textmoy_clim26,
        public readonly ?float $textmoy_clim28,
    ) {}

    public static function create(
        Mois $mois,
        float $epv,
        float $e,
        float $efr26,
        float $efr28,
        float $nref19,
        float $nref21,
        float $nref26,
        float $nref28,
        float $dh14,
        float $dh19,
        float $dh21,
        float $dh26,
        float $dh28,
        ?float $tefs = null,
        ?float $text = null,
        ?float $textmoy_clim26 = null,
        ?float $textmoy_clim28 = null
    ): self {
        return new self(
            mois: $mois,
            epv: $epv,
            e: $e,
            efr26: $efr26,
            efr28: $efr28,
            nref19: $nref19,
            nref21: $nref21,
            nref26: $nref26,
            nref28: $nref28,
            dh14: $dh14,
            dh19: $dh19,
            dh21: $dh21,
            dh26: $dh26,
            dh28: $dh28,
            tefs: $tefs,
            text: $text,
            textmoy_clim26: $textmoy_clim26,
            textmoy_clim28: $textmoy_clim28,
        );
    }
}
