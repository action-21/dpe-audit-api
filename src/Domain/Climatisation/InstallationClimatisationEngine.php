<?php

namespace App\Domain\Climatisation;

use App\Domain\Batiment\Enum\ZoneClimatique;
use App\Domain\Climatisation\Enum\EnergieGenerateur;
use App\Domain\Climatisation\Table\{Seer, SeerRepository};
use App\Domain\Common\Enum\Mois;
use App\Domain\Common\Error\EngineTableError;
use App\Domain\Simulation\SimulationEngine;

final class InstallationClimatisationEngine
{
    private SimulationEngine $context;
    private InstallationClimatisation $input;

    private ?Seer $table_seer = null;

    public function __construct(private SeerRepository $table_seer_repository)
    {
    }

    /**
     * Cfr,gen - Consommation annuelle de refroidissement de l'installation en kWh
     */
    public function cfr(bool $scenario_depensier = false, bool $energie_primaire = false): float
    {
        $cfr = \array_reduce(Mois::cases(), fn (float $carry, Mois $mois): float => $carry += $this->cfr_j($mois, $scenario_depensier), 0);
        $cfr *= $energie_primaire ? ($this->energie()?->facteur_energie_primaire() ?? 1) : 1;
        return $cfr;
    }

    /**
     * Cfr,gen,j - Consommation de refroidissement de l'installation pour le mois j en kWh
     */
    public function cfr_j(Mois $mois, bool $scenario_depensier = false): float
    {
        return 0.9 * ($this->bfr_j($mois, $scenario_depensier) / $this->eer()) * $this->rdim();
    }

    /**
     * EER - Coefficient d'efficience énergétique
     */
    public function eer(): float
    {
        if ($this->seer_saisi()) {
            return $this->seer_saisi() * 0.95;
        }
        if (null === $this->table_seer()) {
            throw new EngineTableError("climatisation . seer");
        }
        return $this->table_seer()->eer;
    }

    /**
     * Rario de dimensionnement de l'installation
     */
    public function rdim(): float
    {
        return $this->surface_utile() / $this->surface_utile_totale();
    }

    /**
     * Valeur de la table climatisation . seer
     */
    public function table_seer(): ?Seer
    {
        return $this->table_seer;
    }

    public function fetch(): void
    {
        $this->table_seer = $this->table_seer_repository->find_by(
            zone_climatique: $this->zone_climatique(),
            annee_installation: $this->annee_installation()
        );
    }

    // * Données d'entrée

    public function zone_climatique(): ZoneClimatique
    {
        return $this->context->input()->batiment()->adresse()->zone_climatique;
    }

    public function surface_utile(): float
    {
        return $this->context->input()->logement_collection()->surface_climatisee_utile(generateur_id: $this->input->id());
    }

    public function surface_utile_totale(): float
    {
        return $this->context->input()->logement_collection()->surface_climatisee_utile();
    }

    public function annee_installation(): ?int
    {
        return $this->input->annee_installation()?->valeur();
    }

    public function annee_installation_defaut(): int
    {
        return $this->annee_installation() ?? $this->context->input()->batiment()->annee_construction()->valeur();
    }

    public function seer_saisi(): ?float
    {
        return $this->input->seer()?->valeur();
    }

    public function energie(): ?EnergieGenerateur
    {
        return $this->input->energie();
    }

    /**
     * @see \App\Domain\Batiment\Engine\Refroidissement
     */
    public function bfr_j(Mois $mois, bool $scenario_depensier = false): float
    {
        return $this->context->batiment_engine()->refroidissement()->bfr_j($mois, $scenario_depensier);
    }

    public function input(): InstallationClimatisation
    {
        return $this->input;
    }

    public function context(): SimulationEngine
    {
        return $this->context;
    }

    public function __invoke(InstallationClimatisation $input, SimulationEngine $context): self
    {
        $engine = clone $this;
        $engine->input = $input;
        $engine->context = $context;
        $engine->fetch();
        return $engine;
    }
}
