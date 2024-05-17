<?php

namespace App\Application\Enveloppe\View;

use App\Domain\Common\Enum\Enum;
use App\Domain\Enveloppe\EnveloppeEngine;

class DeperditionView
{
    public function __construct(
        public readonly ?float $ubat = null,
        public readonly ?float $gv = null,
        public readonly ?float $dp = null,
        public readonly ?float $dr = null,
        public readonly ?float $pt = null,
        public readonly ?float $sdep = null,
        public readonly ?Enum $qualite_isolation = null,
        public readonly ?float $dp_baie = null,
        public readonly ?float $u_baie = null,
        public readonly ?float $sdep_baie = null,
        public readonly ?float $dp_mur = null,
        public readonly ?float $u_mur = null,
        public readonly ?float $sdep_mur = null,
        public readonly ?float $dp_plancher_bas = null,
        public readonly ?float $u_plancher_bas = null,
        public readonly ?float $sdep_plancher_bas = null,
        public readonly ?float $dp_plancher_haut = null,
        public readonly ?float $u_plancher_haut = null,
        public readonly ?float $sdep_plancher_haut = null,
        public readonly ?float $dp_porte = null,
        public readonly ?float $u_porte = null,
        public readonly ?float $sdep_porte = null,
    ) {
    }

    public static function from_engine(EnveloppeEngine $engine): self
    {
        return new self(
            ubat: $engine->deperdition()->ubat(),
            gv: $engine->deperdition()->gv(),
            dp: $engine->deperdition()->dp(),
            dr: $engine->deperdition()->dr(),
            pt: $engine->deperdition()->pt(),
            sdep: $engine->deperdition()->sdep(),
            qualite_isolation: $engine->deperdition()->qualite_isolation(),
            dp_baie: $engine->baie_engine_collection()->dp(),
            u_baie: $engine->baie_engine_collection()->u(),
            sdep_baie: $engine->baie_engine_collection()->sdep(),
            dp_mur: $engine->mur_engine_collection()->dp(),
            u_mur: $engine->mur_engine_collection()->u(),
            sdep_mur: $engine->mur_engine_collection()->sdep(),
            dp_plancher_bas: $engine->plancher_bas_engine_collection()->dp(),
            u_plancher_bas: $engine->plancher_bas_engine_collection()->u(),
            sdep_plancher_bas: $engine->plancher_bas_engine_collection()->sdep(),
            dp_plancher_haut: $engine->plancher_haut_engine_collection()->dp(),
            u_plancher_haut: $engine->plancher_haut_engine_collection()->u(),
            sdep_plancher_haut: $engine->plancher_haut_engine_collection()->sdep(),
            dp_porte: $engine->porte_engine_collection()->dp(),
            u_porte: $engine->porte_engine_collection()->u(),
            sdep_porte: $engine->porte_engine_collection()->sdep(),
        );
    }
}
