<?php

namespace App\Domain\Ecs;

use App\Domain\Batiment\Enum\{TypeBatiment, ZoneClimatique};
use App\Domain\Common\Enum\Mois;
use App\Domain\Common\Error\EngineTableError;
use App\Domain\Ecs\Engine\GenerateurEngineCollection;
use App\Domain\Ecs\Enum\TypeInstallationSolaire;
use App\Domain\Ecs\Table\{Fecs, FecsRepository};
use App\Domain\Simulation\SimulationEngine;

final class InstallationEcsEngine
{
    private SimulationEngine $context;
    private InstallationEcs $input;

    private ?Fecs $table_fecs = null;

    public function __construct(
        private GenerateurEngineCollection $generateur_engine_collection,
        private FecsRepository $table_fecs_repository,
    ) {
    }

    /**
     * Consommation annuelle des générateurs en kWh PCI
     */
    public function cecs(bool $scenario_depensier = false): float
    {
        return $this->generateur_engine_collection->cecs($scenario_depensier);
    }

    /**
     * Consommation des générateurs pour le mois j en kWh PCI
     */
    public function cecs_j(Mois $mois, bool $scenario_depensier = false): float
    {
        return $this->generateur_engine_collection->cecs_j($mois, $scenario_depensier);
    }

    /**
     * Facteur de couverture solaire
     */
    public function fecs(): float
    {
        if ($this->fecs_saisi()) {
            return $this->fecs_saisi();
        }
        if ($this->type_installation_solaire() && null === $this->table_fecs()) {
            throw new EngineTableError('ecs . fecs');
        }
        return $this->type_installation_solaire() ? $this->table_fecs()->fecs : 0;
    }

    /**
     * Valeur de la table ecs . fecs
     */
    public function table_fecs(): ?Fecs
    {
        return $this->table_fecs;
    }

    public function fetch(): void
    {
        $this->table_fecs = $this->type_installation_solaire() ? $this->table_fecs_repository->find_by(
            zone_climatique: $this->zone_climatique(),
            type_batiment: $this->type_batiment(),
            type_installation_solaire: $this->type_installation_solaire(),
        ) : null;
    }

    public function generateur_engine_collection(): GenerateurEngineCollection
    {
        return $this->generateur_engine_collection;
    }

    // * Données d'entrée

    public function zone_climatique(): ZoneClimatique
    {
        return $this->input->logement()->batiment()->adresse()->zone_climatique;
    }

    public function type_batiment(): TypeBatiment
    {
        return $this->input->logement()->batiment()->type_batiment();
    }

    public function type_installation_solaire(): ?TypeInstallationSolaire
    {
        return $this->input->type_installation_solaire();
    }

    public function surface_reference(): float
    {
        return $this->input->logement()->surface_habitable();
    }

    public function fecs_saisi(): ?float
    {
        return $this->input->fecs()?->valeur();
    }

    public function input(): InstallationEcs
    {
        return $this->input;
    }

    public function context(): SimulationEngine
    {
        return $this->context;
    }

    public function __invoke(InstallationEcs $input, SimulationEngine $context): self
    {
        $engine = clone $this;
        $engine->input = $input;
        $engine->context = $context;
        $engine->generateur_engine_collection = ($this->generateur_engine_collection)($input->generateur_collection(), $engine);
        $engine->fetch();
        return $engine;
    }
}
