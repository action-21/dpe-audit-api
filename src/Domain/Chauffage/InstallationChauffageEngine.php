<?php

namespace App\Domain\Chauffage;

use App\Domain\Batiment\Enum\{TypeBatiment, ZoneClimatique};
use App\Domain\Chauffage\Enum\{ConfigurationInstallation};
use App\Domain\Chauffage\Table\{Fch, FchRepository};
use App\Domain\Common\Error\EngineTableError;
use App\Domain\Simulation\SimulationEngine;

final class InstallationChauffageEngine
{
    private InstallationChauffage $input;
    private SimulationEngine $context;

    private ?Fch $table_fch = null;

    public function __construct(private FchRepository $table_fch_repository,)
    {
    }

    /**
     * Fch - Fraction de chauffage couvert par l'installation solaire
     */
    public function fch(): float
    {
        if (false === $this->calcul_fch()) {
            return 0;
        }
        if ($this->fch_saisi()) {
            return $this->fch_saisi();
        }
        return $this->fch_saisi() ?? $this->table_fch()->fch;
    }

    /**
     * Valeur de la table chauffage . fch
     */
    public function table_fch(): ?Fch
    {
        if (false === $this->calcul_fch()) {
            return null;
        }
        if (null === $this->table_fch) {
            throw new EngineTableError('chauffage . fch');
        }
        return $this->table_fch;
    }

    public function calcul_fch(): bool
    {
        return $this->configuration()->chauffage_solaire();
    }

    public function fetch(): void
    {
        $this->table_fch = $this->calcul_fch() ? $this->table_fch_repository->find_by(
            zone_climatique: $this->zone_climatique(),
            type_batiment: $this->type_batiment(),
        ) : null;
    }

    // * Données d'entrée

    public function zone_climatique(): ZoneClimatique
    {
        return $this->context->input()->batiment()->adresse()->zone_climatique;
    }

    public function type_batiment(): TypeBatiment
    {
        return $this->context->input()->batiment()->type_batiment();
    }

    public function configuration(): ConfigurationInstallation
    {
        return $this->input->configuration();
    }

    public function fch_saisi(): ?float
    {
        return $this->input->fch()?->valeur();
    }

    public function input(): InstallationChauffage
    {
        return $this->input;
    }

    public function context(): SimulationEngine
    {
        return $this->context;
    }

    public function __invoke(InstallationChauffage $input, SimulationEngine $context): self
    {
        $engine = clone $this;
        $engine->input = $input;
        $engine->context = $context;
        return $engine;
    }
}
