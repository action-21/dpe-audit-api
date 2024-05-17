<?php

namespace App\Domain\Batiment;

use App\Domain\Batiment\Engine\{Eclairage, Ecs, Occupation, Refroidissement, Situation};
use App\Domain\Simulation\SimulationEngine;

final class BatimentEngine
{
    private Batiment $input;
    private SimulationEngine $context;

    public function __construct(
        private Occupation $occupation,
        private Situation $situation,
        private Eclairage $eclairage,
        private Refroidissement $refroidissement,
        private Ecs $ecs,
    ) {
    }

    public function occupation(): Occupation
    {
        return $this->occupation;
    }

    public function situation(): Situation
    {
        return $this->situation;
    }

    public function eclairage(): Eclairage
    {
        return $this->eclairage;
    }

    public function refroidissement(): Refroidissement
    {
        return $this->refroidissement;
    }

    public function ecs(): Ecs
    {
        return $this->ecs;
    }

    public function context(): SimulationEngine
    {
        return $this->context;
    }

    public function input(): Batiment
    {
        return $this->input;
    }

    public function __invoke(Batiment $input, SimulationEngine $context): self
    {
        $engine = clone $this;
        $engine->input = $input;
        $engine->context = $context;
        $engine->occupation = ($this->occupation)($input, $engine);
        $engine->situation = ($this->situation)($input, $engine);
        $engine->eclairage = ($this->eclairage)($input, $engine);
        $engine->refroidissement = ($this->refroidissement)($input, $engine);
        $engine->ecs = ($this->ecs)($input, $engine);
        return $engine;
    }
}
