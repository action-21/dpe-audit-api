<?php

namespace App\Domain\Logement;

use App\Domain\Simulation\SimulationEngine;

final class LogementEngine
{
    private Logement $input;
    private SimulationEngine $context;

    // * Données d'entrée

    public function input(): Logement
    {
        return $this->input;
    }

    public function context(): SimulationEngine
    {
        return $this->context;
    }

    public function __invoke(Logement $input, SimulationEngine $context): self
    {
        $engine = clone $this;
        $engine->input = $input;
        $engine->context = $context;
        return $engine;
    }
}
