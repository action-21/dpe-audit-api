<?php

namespace App\Domain\Photovoltaique;

use App\Domain\Common\Enum\Mois;
use App\Domain\Photovoltaique\Engine\PanneauPhotovoltaiqueEngineCollection;
use App\Domain\Simulation\SimulationEngine;

final class InstallationPhotovoltaiqueEngine
{
    private SimulationEngine $context;
    private InstallationPhotovoltaique $input;

    public function __construct(
        private PanneauPhotovoltaiqueEngineCollection $panneau_photovoltaique_engine_collection
    ) {
    }

    /**
     * Production d'électricité photovoltaïque annuelle en kWh
     */
    public function ppv(): float
    {
        return $this->panneau_photovoltaique_engine_collection->ppv();
    }

    /**
     * Production d'électricité photovoltaïque pour le mois j en kWh
     */
    public function ppv_j(Mois $mois): float
    {
        return $this->panneau_photovoltaique_engine_collection->ppv_j($mois);
    }

    public function panneau_photovoltaique_engine_collection(): PanneauPhotovoltaiqueEngineCollection
    {
        return $this->panneau_photovoltaique_engine_collection;
    }

    public function input(): InstallationPhotovoltaique
    {
        return $this->input;
    }

    public function context(): SimulationEngine
    {
        return $this->context;
    }

    public function __invoke(InstallationPhotovoltaique $input, SimulationEngine $context): self
    {
        $engine = clone $this;
        $engine->input = $input;
        $engine->context = $context;
        $engine->panneau_photovoltaique_engine_collection = ($this->panneau_photovoltaique_engine_collection)($input->panneau_photovoltaique_collection(), $engine);
        return $engine;
    }
}
