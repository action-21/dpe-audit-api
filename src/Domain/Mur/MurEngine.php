<?php

namespace App\Domain\Mur;

use App\Domain\Batiment\Enum\ZoneClimatique;
use App\Domain\Common\Error\{EngineTableError, EngineValeurError};
use App\Domain\Common\ValueObject\Id;
use App\Domain\Mur\Enum\{Mitoyennete, QualiteComposant, TypeDoublage, TypeIsolation, TypeMur};
use App\Domain\Mur\Table\{B, BRepository, Umur, UmurRepository, Umur0Collection, Umur0Repository};
use App\Domain\Simulation\SimulationEngine;

/**
 * @see §3.2.1 - Calcul des Umur
 */
final class MurEngine
{
    /**
     * Lambda par défaut des murs isolés
     */
    final public const LAMBDA_MUR_DEFAUT = 0.04;

    /**
     * Résistance additionnelle dûe à la présence d'un enduit sur une paroi ancienne
     */
    final public const RESISTANCE_ENDUIT_PAROI_ANCIENNE = 0.7;

    private SimulationEngine $context;
    private Mur $input;

    private ?B $table_b = null;
    private ?Umur0Collection $table_umur0_collection = null;
    private ?Umur $table_umur = null;

    public function __construct(
        private BRepository $table_b_repository,
        private Umur0Repository $table_umur0_repository,
        private UmurRepository $table_umur_repository,
    ) {
    }

    /**
     * DP,mur - Déperditions thermiques (W/K)
     */
    public function dp(): float
    {
        return $this->u() * $this->sdep() * $this->b();
    }

    /**
     * u,mur - Coefficient de transmission thermique (W/(m².K))
     */
    public function u(): float
    {
        if ($this->umur_saisi()) {
            return $this->umur_saisi();
        }
        if ($this->type_isolation()->inconnu()) {
            if (null === $this->table_umur()) {
                throw new EngineTableError('mur . umur');
            }
            return \min($this->u0(), $this->table_umur()->valeur());
        }
        if (false === $this->est_isole()) {
            return $this->u0();
        }
        if ($this->est_isole() && null !== $this->resistance_thermique()) {
            return 1 / (1 / $this->u0() + $this->resistance_thermique());
        }
        if ($this->est_isole() && $this->epaisseur_isolant()) {
            return 1 / (1 / $this->u0() + $this->epaisseur_isolant() / self::LAMBDA_MUR_DEFAUT);
        }
        if (null === $this->table_umur()) {
            throw new EngineTableError('mur . umur');
        }
        return \min($this->u0(), $this->table_umur()->valeur());
    }

    /**
     * u0,mur - Coefficient de transmission thermique de la paroi nue (W/(m².K))
     */
    public function u0(): float
    {
        if ($this->umur0_saisi()) {
            return $this->umur0_saisi();
        }
        if (0 === $this->table_umur0_collection()->count()) {
            throw new EngineTableError('mur . umur0');
        }
        return $this->table_umur0_collection()->umur0(
            epaisseur_structure: $this->epaisseur(),
        );
    }

    /**
     * b,paroi - Coefficient de réduction thermique
     * @see \App\Domain\Lnc\LncEngineCollection
     */
    public function b(): float
    {
        if (null === $this->local_non_chauffe_id()) {
            if (null === $this->table_b()) {
                throw new EngineTableError('mur . b');
            }
            return $this->table_b()->valeur();
        }
        if (null === $value = $this->context->local_non_chauffe_engine_collection()->b($this->local_non_chauffe_id())) {
            throw new EngineValeurError('mur . b');
        }
        return $value;
    }

    /**
     * Sdep, mur - Surface déperditive (m²)
     */
    public function sdep(): float
    {
        return $this->surface_reference();
    }

    /**
     * Résistance thermique additionnelle du doublage (m2.K/W)
     */
    public function resistance_doublage(): float
    {
        return $this->type_doublage()->resistance_doublage();
    }

    /**
     * Résistance thermique additionnelle des parois anciennes (m2. K/W)
     */
    public function resistance_paroi_ancienne(): float
    {
        return $this->enduit_isolant() && $this->paroi_ancienne() ? self::RESISTANCE_ENDUIT_PAROI_ANCIENNE : 0;
    }

    /**
     * Indicateur de performance de l'élément
     */
    public function qualite_isolation(): QualiteComposant
    {
        return QualiteComposant::from_umur($this->u());
    }

    /**
     * Valeur de la table paroi . b
     */
    public function table_b(): ?B
    {
        return $this->table_b;
    }

    public function table_umur0_collection(): Umur0Collection
    {
        return $this->table_umur0_collection;
    }

    /**
     * Valeur de la table mur . umur
     */
    public function table_umur(): ?Umur
    {
        return $this->table_umur;
    }

    public function fetch(): void
    {
        $this->table_b = $this->table_b_repository->find_by(mitoyennete: $this->mitoyennete());

        $this->table_umur0_collection = $this->table_umur0_repository->search_by(
            type_mur: $this->type_mur(),
        );

        $this->table_umur = $this->table_umur_repository->find_by(
            zone_climatique: $this->zone_climatique(),
            annee_construction_isolation: $this->annee_construction_isolation(),
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

    public function type_mur(): TypeMur
    {
        return $this->input->caracteristique()->type_mur;
    }

    public function epaisseur(): float
    {
        return $this->input->epaisseur_structure();
    }

    public function enduit_isolant(): bool
    {
        return $this->input->caracteristique()->enduit_isolant;
    }

    public function paroi_ancienne(): bool
    {
        return $this->input->caracteristique()->paroi_ancienne;
    }

    public function type_doublage(): TypeDoublage
    {
        return $this->input->caracteristique()->type_doublage;
    }

    public function type_isolation(): TypeIsolation
    {
        return $this->input->isolation()->type_isolation;
    }

    public function est_isole(): bool
    {
        return $this->type_isolation()->est_isole();
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

    public function surface_totale(): float
    {
        return $this->input->caracteristique()->surface->valeur();
    }

    public function umur0_saisi(): ?float
    {
        return $this->input->caracteristique()->umur0?->valeur();
    }

    public function umur_saisi(): ?float
    {
        return $this->input->caracteristique()->umur?->valeur();
    }

    public function input(): Mur
    {
        return $this->input;
    }

    public function context(): SimulationEngine
    {
        return $this->context;
    }

    public function __invoke(Mur $input, SimulationEngine $context): self
    {
        $engine = clone $this;
        $engine->input = $input;
        $engine->context = $context;
        $engine->fetch();
        return $engine;
    }
}
