<?php

namespace App\Domain\Batiment\Engine;

use App\Domain\Batiment\{Batiment, BatimentEngine};
use App\Domain\Common\Enum\Mois;

final class Chauffage
{
    private Batiment $input;
    private BatimentEngine $engine;

    /**
     * BV,j - Besoin de chauffage du bâtiment en W/K
     */
    public function bv_j(Mois $mois, bool $scenario_depensier = false): float
    {
        return $this->gv() * (1 - $this->f_j($mois, $scenario_depensier));
    }

    /**
     * @see Situation
     */
    public function dh_ch_j(Mois $mois, bool $scenario_depensier = false): null|float
    {
        return $this->engine->situation()->dh_ch_j($mois, $scenario_depensier);
    }

    /**
     * @see \App\Domain\Enveloppe\Engine\Deperdition
     */
    public function gv(): float
    {
        return $this->engine->context()->enveloppe_engine()?->deperdition()->gv();
    }

    /**
     * @see \App\Domain\Enveloppe\Engine\Deperdition
     */
    public function f_j(Mois $mois, bool $scenario_depensier = false): float
    {
        return $this->engine->context()->enveloppe_engine()->apport()->f_j(mois: $mois, scenario_depensier: $scenario_depensier);
    }

    public function input(): Batiment
    {
        return $this->input;
    }

    public function engine(): BatimentEngine
    {
        return $this->engine;
    }

    public function __invoke(Batiment $input, BatimentEngine $engine): self
    {
        $service = clone $this;
        $service->input = $input;
        $service->engine = $engine;
        return $service;
    }
}
