<?php

namespace App\Domain\Audit;

use App\Domain\Audit\Enum\PerimetreApplication;
use App\Domain\Batiment\BatimentEngine;
use App\Domain\Simulation\SimulationEngine;

final class AuditEngine
{
    private Audit $input;
    private SimulationEngine $context;

    public function __construct(
        private BatimentEngine $batiment_engine,
    ) {
    }

    public function surface_reference(): float
    {
        return $this->input->perimetre_application() === PerimetreApplication::IMMEUBLE
            ? $this->context->input()->batiment()->surface_habitable()
            : $this->context->input()->logement_collection()->surface_habitable();
    }

    // * Données d'entrée

    public function input(): Audit
    {
        return $this->input;
    }

    public function context(): SimulationEngine
    {
        return $this->context;
    }

    public function __invoke(Audit $input, SimulationEngine $context): self
    {
        $engine = clone $this;
        $engine->input = $input;
        $engine->context = $context;
        return $engine;
    }
}
