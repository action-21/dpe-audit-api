<?php

namespace App\Domain\Batiment\Engine;

use App\Domain\Batiment\{Batiment, BatimentEngine};
use App\Domain\Climatisation\InstallationClimatisationEngineCollection;
use App\Domain\Common\Enum\Mois;
use App\Domain\Enveloppe\Enum\ClasseInertie;

/**
 * @see §10.1
 * @see §10.2
 */
final class Refroidissement
{
    private Batiment $input;
    private BatimentEngine $engine;

    /**
     * ∑cfr - Somme des consommations de refroidissement (kWh)
     */
    public function cfr(bool $scenario_depensier = false, bool $energie_primaire = false): float
    {
        return $this->installation_climatisation_engine_collection()->cfr($scenario_depensier, $energie_primaire);
    }

    /**
     * ∑cfr,j - Somme des consommations de refroidissement pour le mois j (kWh)
     */
    public function cfr_j(Mois $mois, bool $scenario_depensier = false): float
    {
        return $this->installation_climatisation_engine_collection()->cfr_j($mois, $scenario_depensier);
    }

    /**
     * Bfr - Besoin annuel de refroidissement en Wh
     */
    public function bfr(bool $scenario_depensier = false): float
    {
        return \array_reduce(Mois::cases(), fn (float $carry, Mois $mois): float => $carry += $this->bfr_j($mois, $scenario_depensier), 0);
    }

    /**
     * Bfr,j - Besoin de refroidissement pour le mois j en Wh
     */
    public function bfr_j(Mois $mois, bool $scenario_depensier = false): float
    {
        if ((1 / 2) > $this->rbth_j($mois, $scenario_depensier)) {
            return 0;
        }
        $bfr_j = $this->apport_fr_j($mois, $scenario_depensier) / 1000;
        $bfr_j -= $this->fut_j($mois, $scenario_depensier) * ($this->gv() / 1000) * ($this->tint($scenario_depensier) - $this->text_moy_clim_j($mois, $scenario_depensier)) * $this->nref_fr_j($mois, $scenario_depensier);
        return $bfr_j;
    }

    /**
     * fut,j - Facteur d'utilisation des apports sur le mois j
     */
    public function fut_j(Mois $mois, bool $scenario_depensier = false): null|float
    {
        if (null === $rbth_j = $this->rbth_j($mois, $scenario_depensier)) {
            return null;
        }
        $alpha = 1 + $this->t() / 15;

        if ($rbth_j === 1) {
            return $alpha / ($alpha + 1);
        }
        return (1 - \pow($rbth_j, $alpha * (-1))) / (1 - \pow($rbth_j, $alpha * (-1) - 1));
    }

    /**
     * Rbth,j - Ratio de bilan thermique sur le mois j
     */
    public function rbth_j(Mois $mois, bool $scenario_depensier = false): null|float
    {
        if (null === $text_moy_clim_j = $this->text_moy_clim_j($mois, $scenario_depensier)) {
            return null;
        }
        if (null === $nref_fr_j = $this->nref_fr_j($mois, $scenario_depensier)) {
            return null;
        }
        return $this->apport_fr_j($mois, $scenario_depensier) / (
            $this->gv() * ($text_moy_clim_j - $this->tint($scenario_depensier)) * $nref_fr_j
        );
    }

    /**
     * t - Constante de temps de la zone pour le refroidissement
     */
    public function t(): float
    {
        return $this->cin() / (3600 * $this->gv());
    }

    /**
     * Cin - Capacité thermique intérieure efficace de la zone en J/K
     */
    public function cin(): float
    {
        return $this->classe_inertie()->cin() * $this->surface_reference();
    }

    /**
     * Tint - Température intérieur de consigne en °C
     */
    public function tint(bool $scenario_depensier = false): float
    {
        return $scenario_depensier ? 26 : 28;
    }

    // * Données intermédiaires

    public function surface_reference(): float
    {
        return $this->engine->context()->surface_reference();
    }

    /**
     * @see \App\Domain\Enveloppe\Engine\Inertie
     */
    public function classe_inertie(): ClasseInertie
    {
        return $this->engine->context()->enveloppe_engine()->inertie()->classe_inertie();
    }

    /**
     * @see \App\Domain\Enveloppe\Engine\Deperdition
     */
    public function gv(): float
    {
        return $this->engine->context()->enveloppe_engine()->deperdition()->gv();
    }

    /**
     * @see \App\Domain\Enveloppe\Engine\Apport
     */
    public function apport_fr_j(Mois $mois, bool $scenario_depensier = false): null|float
    {
        return $this->engine->context()->enveloppe_engine()->apport()->apport_fr_j(mois: $mois, scenario_depensier: $scenario_depensier);
    }

    /**
     * @see \App\Domain\Enveloppe\Engine\Apport
     */
    public function nref_fr_j(Mois $mois, bool $scenario_depensier = false): null|float
    {
        return $this->engine->context()->enveloppe_engine()->apport()->nref_fr_j(mois: $mois, scenario_depensier: $scenario_depensier);
    }
    /**
     * @see \App\Domain\Batiment\Engine\Situation
     */
    public function text_moy_clim_j(Mois $mois, bool $scenario_depensier = false): null|float
    {
        return $this->engine->context()->batiment_engine()->situation()->text_moy_clim_j(mois: $mois, scenario_depensier: $scenario_depensier);
    }

    public function input(): Batiment
    {
        return $this->input;
    }

    public function engine(): BatimentEngine
    {
        return $this->engine;
    }

    public function installation_climatisation_engine_collection(): InstallationClimatisationEngineCollection
    {
        return $this->engine->context()->installation_climatisation_engine_collection();
    }

    public function __invoke(Batiment $input, BatimentEngine $engine): self
    {
        $service = clone $this;
        $service->input = $input;
        $service->engine = $engine;
        return $service;
    }
}
