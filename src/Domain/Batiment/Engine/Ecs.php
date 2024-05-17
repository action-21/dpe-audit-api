<?php

namespace App\Domain\Batiment\Engine;

use App\Domain\Batiment\{Batiment, BatimentEngine};
use App\Domain\Common\Enum\Mois;
use App\Domain\Ecs\InstallationEcsEngineCollection;

final class Ecs
{
    private Batiment $input;
    private BatimentEngine $engine;

    /**
     * Consommation annuelle des générateurs en kWh PCI
     */
    public function cecs(bool $scenario_depensier = false): float
    {
        return $this->installation_ecs_engine_collection()->cecs($scenario_depensier);
    }

    /**
     * Consommation des générateurs pour le mois j en kWh PCI
     */
    public function cecs_j(Mois $mois, bool $scenario_depensier = false): float
    {
        return $this->installation_ecs_engine_collection()->cecs_j($mois, $scenario_depensier);
    }

    /**
     * becs - Besoin d'eau chaude sanitaire annuel en Wh
     */
    public function becs(bool $scenario_depensier = false): float
    {
        return \array_reduce(Mois::cases(), fn (float $carry, Mois $mois) => $carry += $this->becs_j($mois, $scenario_depensier), 0);
    }

    /**
     * becs,j - Besoin d'eau chaude sanitaire pour le mois j en Wh
     */
    public function becs_j(Mois $mois, bool $scenario_depensier = false): float
    {
        return $scenario_depensier
            ? 1.163 * $this->nadeq() * 79 * (40 - $this->tefs_j($mois)) * $mois->jours_occupation()
            : 1.163 * $this->nadeq() * 56 * (40 - $this->tefs_j($mois)) * $mois->jours_occupation();
    }

    // * Données d'entrée

    /**
     * @see Occupation
     */
    public function nadeq(): float
    {
        return $this->engine->occupation()->nadeq();
    }

    /**
     * @see Situation
     */
    public function tefs_j(Mois $mois): float
    {
        return $this->engine->situation()->tefs_j($mois);
    }

    public function installation_ecs_engine_collection(): InstallationEcsEngineCollection
    {
        return $this->engine->context()->installation_ecs_engine_collection();
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
