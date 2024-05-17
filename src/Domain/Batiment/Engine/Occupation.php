<?php

namespace App\Domain\Batiment\Engine;

use App\Domain\Batiment\{Batiment, BatimentEngine};
use App\Domain\Batiment\Enum\TypeBatiment;

/**
 * @see §11.1 - Calcul du besoin d’ECS
 */
final class Occupation
{
    private Batiment $input;
    private BatimentEngine $engine;

    /**
     * Nadeq - Nombre d'adultes équivalents
     */
    public function nadeq(): float
    {
        return ($nmax = $this->nmax()) < 1.75
            ? $this->nombre_logements() * $nmax
            : $this->nombre_logements() * (1.75 + 0.3 * ($nmax - 1.75));
    }

    /**
     * Nmax - Coefficient d'occupation maximal
     */
    public function nmax(): float
    {
        $sh_moy = $this->surface_reference() / $this->nombre_logements();

        if ($this->type_batiment()->maison()) {
            if ($sh_moy < 30) {
                return 1;
            } else if ($sh_moy < 70) {
                return 1.75 - 0.01875 * (70 - $sh_moy);
            } else {
                return 0.025 * $sh_moy;
            }
        }
        if ($sh_moy < 10) {
            return 1;
        } else if ($sh_moy < 50) {
            return 1.75 - 0.01875 * (50 - $sh_moy);
        } else {
            return 0.035 * $sh_moy;
        }
    }

    // * Données d'entrée

    public function type_batiment(): TypeBatiment
    {
        return $this->input->type_batiment();
    }

    public function surface_reference(): float
    {
        return $this->engine->context()->surface_reference();
    }

    public function nombre_logements(): float
    {
        return $this->engine->context()->nombre_logements_reference();
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
