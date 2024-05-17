<?php

namespace App\Application\Enveloppe\View;

use App\Domain\Common\Table\TableValue;
use App\Domain\Enveloppe\EnveloppeEngine;

class PermeabiliteView
{
    public function __construct(
        public readonly ?float $hvent = null,
        public readonly ?float $hperm = null,
        public readonly ?float $qvinf = null,
        public readonly ?float $n50 = null,
        public readonly ?float $q4pa = null,
        public readonly ?float $q4pa_env = null,
        public readonly ?float $q4pa_conv = null,
        public readonly ?float $qvasouf_conv = null,
        public readonly ?float $qvarep_conv = null,
        public readonly ?float $smea_conv = null,
        public readonly ?float $e = null,
        public readonly ?float $f = null,
        public readonly ?float $sdep = null,
        public readonly ?TableValue $table_q4pa_conv = null,
    ) {
    }

    public static function from_engine(EnveloppeEngine $engine): self
    {
        return new self(
            hvent: $engine->permeabilite()->hvent(),
            hperm: $engine->permeabilite()->hperm(),
            qvinf: $engine->permeabilite()->qvinf(),
            n50: $engine->permeabilite()->n50(),
            q4pa: $engine->permeabilite()->q4pa(),
            q4pa_env: $engine->permeabilite()->q4pa_env(),
            q4pa_conv: $engine->permeabilite()->q4pa_conv(),
            qvasouf_conv: $engine->permeabilite()->qvasouf_conv(),
            qvarep_conv: $engine->permeabilite()->qvarep_conv(),
            smea_conv: $engine->permeabilite()->smea_conv(),
            sdep: $engine->permeabilite()->sdep(),
            e: $engine->permeabilite()->e(),
            f: $engine->permeabilite()->f(),
            table_q4pa_conv: $engine->permeabilite()->table_q4pa_conv(),
        );
    }
}
