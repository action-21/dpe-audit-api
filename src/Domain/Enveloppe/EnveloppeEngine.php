<?php

namespace App\Domain\Enveloppe;

use App\Domain\Enveloppe\Engine\{Apport, Deperdition, Inertie, Permeabilite};
use App\Domain\Enveloppe\Enveloppe;
use App\Domain\Simulation\SimulationEngine;

final class EnveloppeEngine
{
    private Enveloppe $input;
    private SimulationEngine $context;

    public function __construct(
        private Apport $apport,
        private Deperdition $deperdition,
        private Inertie $inertie,
        private Permeabilite $permeabilite,
    ) {
    }

    public function deperdition(): Deperdition
    {
        return $this->deperdition;
    }

    public function apport(): Apport
    {
        return $this->apport;
    }

    public function inertie(): Inertie
    {
        return $this->inertie;
    }

    public function permeabilite(): Permeabilite
    {
        return $this->permeabilite;
    }

    // * Données d'entrée

    public function input(): Enveloppe
    {
        return $this->input;
    }

    public function context(): SimulationEngine
    {
        return $this->context;
    }

    public function __invoke(Enveloppe $input, SimulationEngine $context): self
    {
        $engine = clone $this;
        $engine->input = $input;
        $engine->context = $context;
        $engine->inertie = ($this->inertie)($input, $engine);
        $engine->permeabilite = ($this->permeabilite)($input, $engine);
        $engine->deperdition = ($this->deperdition)($input, $engine);
        $engine->apport = ($this->apport)($input, $engine);
        return $engine;
    }
}
