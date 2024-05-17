<?php

namespace App\Domain\Enveloppe\Engine;

use App\Domain\Enveloppe\Enum\QualiteComposant;
use App\Domain\Enveloppe\{Enveloppe, EnveloppeEngine};

/**
 * @see §3 Calcul des déperditions de l'enveloppe GV
 * 
 * @see \App\Domain\Ventilation\InstallationVentilationEngine
 */
final class Deperdition
{
    private Enveloppe $input;
    private EnveloppeEngine $engine;

    /**
     * Ubat - Performance de l'enveloppe ((W/(m².K))
     */
    public function ubat(): float
    {
        return ($sdep = $this->sdep()) ? ($this->dp() + $this->pt()) / $sdep : 0;
    }

    /**
     * G - déperditions annuelles de l'enveloppe (W/K)
     */
    public function g(): float
    {
        return $this->gv() / $this->engine->context()->volume_reference();
    }

    /**
     * GV - Déperditions thermiques de l'enveloppe (W/K)
     */
    public function gv(): float
    {
        return $this->dp() + $this->pt() + $this->dr();
    }

    /**
     * DR - Déperditions thermiques par le renouvellement d'air (W/K)
     */
    public function dr(): float
    {
        return $this->engine->permeabilite()->hvent() + $this->engine->permeabilite()->hperm();
    }

    /**
     * PT - Déperditions thermiques par les ponts thermiques (W/K)
     */
    public function pt(): float
    {
        return $this->engine->context()->pont_thermique_engine_collection()->pt();
    }

    /**
     * DP - Déperditions thermiques par les parois (W/K)
     */
    public function dp(): float
    {
        return \array_sum([
            $this->engine->context()->baie_engine_collection()->dp(),
            $this->engine->context()->mur_engine_collection()->dp(),
            $this->engine->context()->plancher_haut_engine_collection()->dp(),
            $this->engine->context()->plancher_bas_engine_collection()->dp(),
            $this->engine->context()->porte_engine_collection()->dp(),
        ]);
    }

    /**
     * sdep - Surface déperditive de l'enveloppe (m²)
     */
    public function sdep(): float
    {
        return \array_sum([
            $this->engine->context()->baie_engine_collection()->sdep(),
            $this->engine->context()->mur_engine_collection()->sdep(),
            $this->engine->context()->plancher_haut_engine_collection()->sdep(),
            $this->engine->context()->plancher_bas_engine_collection()->sdep(),
            $this->engine->context()->porte_engine_collection()->sdep(),
        ]);
    }

    /**
     * Indicateur de performance de l'enveloppe
     */
    public function qualite_isolation(): QualiteComposant
    {
        return QualiteComposant::from_ubat($this->ubat());
    }

    public function input(): Enveloppe
    {
        return $this->input;
    }

    public function engine(): EnveloppeEngine
    {
        return $this->engine;
    }

    public function __invoke(Enveloppe $input, EnveloppeEngine $engine): self
    {
        $service = clone $this;
        $service->input = $input;
        $service->engine = $engine;
        return $service;
    }
}
