<?php

namespace App\Domain\Chauffage\Engine;

use App\Domain\Batiment\Enum\TypeBatiment;
use App\Domain\Chauffage\Entity\Emission;
use App\Domain\Chauffage\Enum\{EquipementIntermittence, TemperatureDistribution, TypeChauffage, TypeDistribution, TypeEmission, TypeGenerateur, TypeInstallation, TypeRegulation};
use App\Domain\Chauffage\Table\{I0, I0Repository};
use App\Domain\Chauffage\Table\{Rd, RdRepository};
use App\Domain\Chauffage\Table\{Re, ReRepository};
use App\Domain\Chauffage\Table\{Rr, RrRepository};
use App\Domain\Chauffage\Table\{Tfonc100, Tfonc100Repository};
use App\Domain\Chauffage\Table\{Tfonc30, Tfonc30Repository};
use App\Domain\Common\Error\EngineTableError;
use App\Domain\Enveloppe\Enum\ClasseInertie;

final class EmissionEngine
{
    private GenerateurEngine $engine;
    private Emission $input;

    private ?I0 $table_i0 = null;
    private ?Rd $table_rd = null;
    private ?Re $table_re = null;
    private ?Rr $table_rr = null;
    private ?Tfonc30 $table_tfonc30 = null;
    private ?Tfonc100 $table_tfonc100 = null;

    public function __construct(
        private I0Repository $table_i0_repository,
        private RdRepository $table_rd_repository,
        private ReRepository $table_re_repository,
        private RrRepository $table_rr_repository,
        private Tfonc30Repository $table_tfonc30_repository,
        private Tfonc100Repository $table_tfonc100_repository,
    ) {
    }

    /**
     * INT - Facteur d'intermittence
     */
    public function int(): float
    {
        return $this->i0() / (1 + 0.1 * ($this->g() - 1));
    }

    /**
     * ich,e - Inverse du rendement de chauffage de l'emission
     */
    public function ich_emission(): float
    {
        return 1 / ($this->re() * $this->rd() * $this->rr());
    }

    /**
     * i0 - Coefficient d'intermittence
     */
    public function i0(): float
    {
        return $this->table_i0()->i0;
    }

    /**
     * rd - Rendement de distribution
     */
    public function rd(): float
    {
        return $this->table_rd()->rd;
    }

    /**
     * re - Rendement d'émission
     */
    public function re(): float
    {
        return $this->table_re()->re;
    }

    /**
     * rr - Rendement de régulation
     */
    public function rr(): float
    {
        return $this->table_rr()->rr;
    }

    /**
     * tfonc30 - Température de fonctionnement à 30% de charge en °C
     */
    public function tfonc30(): ?float
    {
        return $this->table_tfonc30()?->tfonc30;
    }

    /**
     * tfonc100 - Température de fonctionnement à 100% de charge en °C
     */
    public function tfonc100(): ?float
    {
        return $this->table_tfonc100()?->tfonc100;
    }

    /**
     * Ratio de dimensionnement de l'émission
     */
    public function rdim(): float
    {
        return $this->surface_emission() / $this->surface_emission_generateur();
    }

    /**
     * Scénario de calcul de tfonc30 et tfonc100
     */
    public function calcul_tfonc(): bool
    {
        return $this->type_generateur()->combustion_standard()
            || $this->type_generateur()->combustion_basse_temperature()
            || $this->type_generateur()->combustion_condensation();
    }

    /**
     * Valeur de la table chauffage . émission . i0
     */
    public function table_i0(): I0
    {
        if (null === $this->table_i0) {
            throw new EngineTableError('chauffage . émisssion . i0');
        }
        return $this->table_i0;
    }

    /**
     * Valeur de la table chauffage . émission . rd
     */
    public function table_rd(): Rd
    {
        if (null === $this->table_rd) {
            throw new EngineTableError('chauffage . émisssion . rd');
        }
        return $this->table_rd;
    }

    /**
     * Valeur de la table chauffage . émission . re
     */
    public function table_re(): Re
    {
        if (null === $this->table_re) {
            throw new EngineTableError('chauffage . émisssion . re');
        }
        return $this->table_re;
    }

    /**
     * Valeur de la table chauffage . émission . rr
     */
    public function table_rr(): Rr
    {
        if (null === $this->table_rr) {
            throw new EngineTableError('chauffage . émisssion . rr');
        }
        return $this->table_rr;
    }

    /**
     * Valeur de la table chauffage . émission . tfonc30
     */
    public function table_tfonc30(): ?Tfonc30
    {
        if (false === $this->calcul_tfonc()) {
            return null;
        }
        if (null === $this->table_tfonc30) {
            throw new EngineTableError('chauffage . émisssion . tfonc30');
        }
        return $this->table_tfonc30;
    }

    /**
     * Valeur de la table chauffage . émission . tfonc100
     */
    public function table_tfonc100(): ?Tfonc100
    {
        if (false === $this->calcul_tfonc()) {
            return null;
        }
        if (null === $this->table_tfonc100) {
            throw new EngineTableError('chauffage . émisssion . tfonc100');
        }
        return $this->table_tfonc100;
    }

    public function fetch(): void
    {
        $this->table_i0 = $this->table_i0_repository->find_by(
            type_batiment: $this->type_batiment(),
            type_installation: $this->type_installation(),
            type_chauffage: $this->type_chauffage(),
            equipement_intermittence: $this->equipement_intermittence(),
            type_regulation: $this->type_regulation(),
            type_emission: $this->type_emission(),
            inertie: $this->inertie(),
            comptage_individuel: $this->comptage_individuel(),
        );

        $this->table_rd = $this->table_rd_repository->find_by(
            type_installation: $this->type_installation(),
            type_distribution: $this->type_distribution(),
            temperature_distribution: $this->temperature_distribution(),
            reseau_distribution_isole: $this->reseau_distribution_isole(),
        );

        $this->table_re = $this->table_re_repository->find_by(
            type_emission: $this->type_emission(),
            type_generateur: $this->type_generateur(),
        );

        $this->table_rr = $this->table_rr_repository->find_by(
            type_installation: $this->type_installation(),
            type_emission: $this->type_emission(),
            type_distribution: $this->type_distribution(),
            type_generateur: $this->type_generateur(),
        );

        $this->table_tfonc30 = $this->calcul_tfonc() ? $this->table_tfonc30_repository->find_by(
            type_generateur: $this->type_generateur(),
            temperature_distribution: $this->temperature_distribution(),
            annee_installation_emetteur: $this->annee_installation_emission(),
        ) : null;

        $this->table_tfonc100 = $this->calcul_tfonc() ? $this->table_tfonc100_repository->find_by(
            temperature_distribution: $this->temperature_distribution(),
            annee_installation_emetteur: $this->annee_installation_emission(),
        ) : null;
    }

    // * Données d'entrée

    /**
     * @see \App\Domain\Enveloppe\Engine\Deperdition
     */
    public function g(): float
    {
        return $this->engine->engine()->context()->enveloppe_engine()->deperdition()->g();
    }

    public function type_batiment(): TypeBatiment
    {
        return $this->engine->engine()->context()->batiment_engine()->input()->type_batiment();
    }

    public function inertie(): ClasseInertie
    {
        return $this->engine->engine()->context()->enveloppe_engine()->inertie()->classe_inertie();
    }

    public function type_installation(): TypeInstallation
    {
        return $this->input->installation()->type_installation();
    }

    public function type_chauffage(): TypeChauffage
    {
        return $this->input->generateur()->type_chauffage();
    }

    public function type_generateur(): TypeGenerateur
    {
        return $this->input->generateur()->type_generateur();
    }

    public function type_distribution(): TypeDistribution
    {
        return $this->input->type_distribution();
    }

    public function type_emission(): TypeEmission
    {
        return $this->input->type_emission();
    }

    public function temperature_distribution(): TemperatureDistribution
    {
        return $this->input->temperature_distribution();
    }

    public function equipement_intermittence(): EquipementIntermittence
    {
        return $this->input->equipement_intermittence();
    }

    public function type_regulation(): TypeRegulation
    {
        return $this->input->type_regulation();
    }

    public function surface_emission(): float
    {
        return $this->input->surface()->valeur();
    }

    public function surface_emission_generateur(): float
    {
        return $this->input->generateur()->emission_collection()->surface();
    }

    public function reseau_distribution_isole(): ?bool
    {
        return $this->input->reseau_distribution_isole();
    }

    public function comptage_individuel(): ?bool
    {
        return $this->input->installation()->comptage_individuel();
    }

    public function annee_installation_emission(): int
    {
        return $this->input->annee_installation()?->valeur()
            ?? $this->engine->engine()->context()->batiment_engine()->input()->annee_construction()->valeur();
    }

    public function input(): Emission
    {
        return $this->input;
    }

    public function engine(): GenerateurEngine
    {
        return $this->engine;
    }

    public function __invoke(Emission $input, GenerateurEngine $engine): self
    {
        $service = clone $this;
        $service->input = $input;
        $service->engine = $engine;
        $service->fetch();
        return $service;
    }
}
