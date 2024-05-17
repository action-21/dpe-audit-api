<?php

namespace App\Domain\PlancherHaut;

use App\Domain\Batiment\Enum\ZoneClimatique;
use App\Domain\Common\Error\{EngineTableError, EngineValeurError};
use App\Domain\Common\ValueObject\Id;
use App\Domain\PlancherHaut\Enum\{ConfigurationPlancherHaut, Mitoyennete, QualiteComposant, TypeIsolation, TypePlancherHaut};
use App\Domain\PlancherHaut\Table\{B, BRepository, Uph, Uph0, UphRepository, Uph0Repository};
use App\Domain\Simulation\SimulationEngine;

/**
 * @see §3.2.3 - Calcul des Uplancher haut (Uph)
 */
final class PlancherHautEngine
{
    /**
     * Lambda par défaut des planchers hauts isolés
     */
    final public const LAMBDA_PLANCHER_HAUT_DEFAUT = 0.04;

    private SimulationEngine $context;
    private PlancherHaut $input;

    private ?B $table_b = null;
    private ?Uph0 $table_uph0 = null;
    private ?Uph $table_uph = null;

    public function __construct(
        private BRepository $table_b_repository,
        private Uph0Repository $table_uph0_repository,
        private UphRepository $table_uph_repository,
    ) {
    }

    /**
     * DP,ph - Déperditions thermiques (W/K)
     */
    public function dp(): float
    {
        return $this->u() * $this->sdep() * $this->b();
    }

    /**
     * u,ph - Coefficient de transmission thermique (W/(m².K))
     */
    public function u(): float
    {
        if ($this->uph_saisi()) {
            return $this->uph_saisi();
        }
        if ($this->type_isolation()->inconnu()) {
            if (null === $this->table_uph()) {
                throw new EngineTableError('plancher haut . uph');
            }
            return \min($this->u0(), $this->table_uph()->valeur());
        }
        if (false === $this->type_isolation()->est_isole()) {
            return $this->u0();
        }
        if ($this->resistance_thermique()) {
            return 1 / (1 / $this->u0() + $this->resistance_thermique());
        }
        if ($this->epaisseur_isolant()) {
            return 1 / (1 / $this->u0() + $this->epaisseur_isolant() / self::LAMBDA_PLANCHER_HAUT_DEFAUT);
        }
        if (null === $this->table_uph()) {
            throw new EngineTableError('plancher haut . uph');
        }
        return \min($this->u0(), $this->table_uph()->valeur());
    }

    /**
     * u0,ph - Coefficient de transmission thermique de la paroi nue (W/(m².K))
     */
    public function u0(): float
    {
        if ($this->uph0_saisi()) {
            return $this->uph0_saisi();
        }
        if (null === $this->table_uph0()) {
            throw new EngineTableError('plancher haut . uph0');
        }
        return $this->table_uph0()->valeur();
    }

    /**
     * b,paroi - Coefficient de réduction thermique
     * @see \App\Domain\Lnc\LncEngineCollection
     */
    public function b(): float
    {
        if (null === $this->local_non_chauffe_id()) {
            if (null === $this->table_b()) {
                throw new EngineTableError('plancher haut . b');
            }
            return $this->table_b()->valeur();
        }
        if (null === $value = $this->context->local_non_chauffe_engine_collection()->b($this->local_non_chauffe_id())) {
            throw new EngineValeurError('plancher haut . b');
        }
        return $value;
    }

    /**
     * Surface déperditive (m²)
     */
    public function sdep(): float
    {
        return $this->surface_reference();
    }

    /**
     * Indicateur de performance de l'élément
     */
    public function qualite_isolation(): QualiteComposant
    {
        return QualiteComposant::from_uph($this->u());
    }

    /**
     * Valeur de la table paroi . b
     */
    public function table_b(): ?B
    {
        return $this->table_b;
    }

    /**
     * Valeur de la table plancher haut . uph0
     */
    public function table_uph0(): ?Uph0
    {
        return $this->table_uph0;
    }

    /**
     * Valeur de la table plancher haut . uph
     */
    public function table_uph(): ?Uph
    {
        return $this->table_uph;
    }

    public function fetch(): void
    {
        $this->table_b = $this->table_b_repository->find_by(mitoyennete: $this->mitoyennete());

        $this->table_uph0 = $this->table_uph0_repository->find_by(
            type_plancher_haut: $this->type_plancher_haut()
        );

        $this->table_uph = $this->table_uph_repository->find_by(
            zone_climatique: $this->zone_climatique(),
            annee_construction_isolation: $this->annee_construction_isolation(),
            configuration_plancher_haut: $this->configuration(),
            effet_joule: $this->effet_joule(),
        );
    }

    // * Données d'entrée

    public function local_non_chauffe_id(): ?Id
    {
        return $this->input->local_non_chauffe()?->id();
    }

    public function zone_climatique(): ZoneClimatique
    {
        return $this->context->input()->batiment()->adresse()->zone_climatique;
    }

    public function effet_joule(): bool
    {
        return $this->context->input()->chauffage_collection()->effet_joule();
    }

    public function mitoyennete(): Mitoyennete
    {
        return $this->input->mitoyennete();
    }

    public function configuration(): ConfigurationPlancherHaut
    {
        return $this->input->configuration();
    }

    public function type_plancher_haut(): TypePlancherHaut
    {
        return $this->input->caracteristique()->type_plancher_haut;
    }

    public function type_isolation(): TypeIsolation
    {
        return $this->input->isolation()->type_isolation;
    }

    public function epaisseur_isolant(): ?float
    {
        return $this->input->isolation()->epaisseur_isolant?->to_metre();
    }

    public function resistance_thermique(): ?float
    {
        return $this->input->isolation()->resistance_thermique?->valeur();
    }

    public function annee_construction(): int
    {
        return $this->context->input()->batiment()->annee_construction()->valeur();
    }

    public function annee_construction_isolation(): int
    {
        return $this->input->annnee_isolation_defaut() ?? $this->annee_construction();
    }

    public function surface_reference(): float
    {
        return $this->input->surface_deperditive();
    }

    public function uph0_saisi(): ?float
    {
        return $this->input->caracteristique()->uph0?->valeur();
    }

    public function uph_saisi(): ?float
    {
        return $this->input->caracteristique()->uph?->valeur();
    }

    public function input(): PlancherHaut
    {
        return $this->input;
    }

    public function context(): SimulationEngine
    {
        return $this->context;
    }

    public function __invoke(PlancherHaut $input, SimulationEngine $context): self
    {
        $engine = clone $this;
        $engine->input = $input;
        $engine->context = $context;
        $engine->fetch();
        return $engine;
    }
}
