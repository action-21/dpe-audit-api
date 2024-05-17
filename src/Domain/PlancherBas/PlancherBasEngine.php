<?php

namespace App\Domain\PlancherBas;

use App\Domain\Batiment\Enum\ZoneClimatique;
use App\Domain\Common\Error\{EngineTableError, EngineValeurError};
use App\Domain\Common\ValueObject\Id;
use App\Domain\PlancherBas\Enum\{Mitoyennete, QualiteComposant, TypePlancherBas, TypeIsolation};
use App\Domain\PlancherBas\Table\{B, BRepository, Upb, UpbRepository, Upb0, Upb0Repository, UeCollection, UeRepository};
use App\Domain\Simulation\SimulationEngine;

/**
 * @see §3.2.2 - Calcul des Uplancher bas (Upb)
 */
final class PlancherBasEngine
{
    /**
     * Lambda par défaut des planchers bas isolés
     */
    final public const LAMBDA_PLANCHER_BAS_DEFAUT = 0.042;

    private SimulationEngine $context;
    private PlancherBas $input;

    private ?B $table_b = null;
    private ?Upb0 $table_upb0 = null;
    private ?Upb $table_upb = null;
    private ?UeCollection $table_ue_collection = null;

    public function __construct(
        private BRepository $table_b_repository,
        private Upb0Repository $table_upb0_repository,
        private UpbRepository $table_upb_repository,
        private UeRepository $table_ue_repository,
    ) {
    }

    /**
     * DP,pb - Déperditions thermiques (W/K)
     */
    public function dp(): float
    {
        return $this->ufinal() * $this->sdep() * $this->b();
    }

    /**
     * u,pb,final - Coefficient de transmission thermique final (W/(m².K))
     */
    public function ufinal(): float
    {
        return $this->calcul_ue() ? $this->ue() : $this->u();
    }

    /**
     * u,pb - Coefficient de transmission thermique (W/(m².K))
     */
    public function u(): float
    {
        if ($this->upb_saisi()) {
            return $this->upb_saisi();
        }
        if ($this->type_isolation()->inconnu()) {
            if (null === $this->table_upb()) {
                throw new EngineTableError('plancher bas . upb');
            }
            return \min($this->u0(), $this->table_upb()->valeur());
        }
        if (false === $this->type_isolation()->est_isole()) {
            return $this->u0();
        }
        if ($this->resistance_thermique()) {
            return 1 / (1 / $this->u0() + $this->resistance_thermique());
        }
        if ($this->epaisseur_isolant()) {
            return 1 / (1 / $this->u0() + $this->epaisseur_isolant() / self::LAMBDA_PLANCHER_BAS_DEFAUT);
        }
        if (null === $this->table_upb()) {
            throw new EngineTableError('plancher bas . upb');
        }
        return \min($this->u0(), $this->table_upb()->valeur());
    }

    /**
     * u0,pb - Coefficient de transmission thermique de la paroi nue (W/(m².K))
     */
    public function u0(): float
    {
        if ($this->upb0_saisi()) {
            return $this->upb0_saisi();
        }
        if (null === $this->table_upb0()) {
            throw new EngineTableError('plancher bas . upb0');
        }
        return $this->table_upb0()->valeur();
    }

    /**
     * ue,pb - Coefficient de transmission thermique du plancher bas sur terre-plain, sous-sol non chauffé et vide sanitaire (W/m².K)
     */
    public function ue(): null|float
    {
        if (false === $this->calcul_ue()) {
            return null;
        }
        if (0 === $this->table_ue_collection()->count()) {
            throw new EngineTableError('plancher bas . ue');
        }
        return $this->table_ue_collection()->ue(
            upb: $this->u(),
            surface: $this->surface_totale(),
            perimetre: $this->perimetre(),
        );
    }

    /**
     * Prise en compte de ue pour le calcul de u
     */
    public function calcul_ue(): bool
    {
        return \in_array($this->mitoyennete(), [Mitoyennete::TERRE_PLEIN, Mitoyennete::VIDE_SANITAIRE], true);
    }

    /**
     * b,pb - Coefficient de réduction thermique
     * @see \App\Domain\Lnc\LncEngineCollection
     */
    public function b(): float
    {
        if (null === $this->local_non_chauffe_id()) {
            if (null === $this->table_b()) {
                throw new EngineTableError('plancher bas . b');
            }
            return $this->table_b()->valeur();
        }
        if (null === $value = $this->context->local_non_chauffe_engine_collection()->b($this->local_non_chauffe_id())) {
            throw new EngineValeurError('plancher bas . b');
        }
        return $value;
    }

    /**
     * Sdep,pb - Surface déperditive (m²)
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
        return QualiteComposant::from_upb($this->ufinal());
    }

    /**
     * Valeurs de la table paroi . b
     */
    public function table_b(): ?B
    {
        return $this->table_b;
    }

    /**
     * Valeur de la table plancher bas . upb0
     */
    public function table_upb0(): ?Upb0
    {
        return $this->table_upb0;
    }

    /**
     * Valeur de la table plancher bas . upb
     */
    public function table_upb(): ?Upb
    {
        return $this->table_upb;
    }

    /**
     * Valeurs de la table plancher bas . ue
     */
    public function table_ue_collection(): UeCollection
    {
        return $this->table_ue_collection;
    }

    public function fetch(): void
    {
        $this->table_b = $this->table_b_repository->find_by(mitoyennete: $this->mitoyennete());

        $this->table_upb0 = $this->table_upb0_repository->find_by(
            type_plancher_bas: $this->type_plancher_bas()
        );

        $this->table_upb = $this->table_upb_repository->find_by(
            zone_climatique: $this->zone_climatique(),
            annee_construction_isolation: $this->annee_construction_isolation(),
            effet_joule: $this->effet_joule(),
        );

        $this->table_ue_collection = $this->calcul_ue() ? $this->table_ue_repository->search_by(
            mitoyennete: $this->mitoyennete(),
            annee_construction: $this->annee_construction(),
        ) : null;
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
        return $this->context->input()->installation_chauffage_collection()->effet_joule();
    }

    public function mitoyennete(): Mitoyennete
    {
        return $this->input->mitoyennete();
    }

    public function type_plancher_bas(): TypePlancherBas
    {
        return $this->input->caracteristique()->type_plancher_bas;
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

    public function surface_totale(): float
    {
        return $this->input->caracteristique()->surface->valeur();
    }

    public function perimetre(): float
    {
        return $this->input->caracteristique()->perimetre->valeur();
    }

    public function upb0_saisi(): ?float
    {
        return $this->input->caracteristique()->upb0?->valeur();
    }

    public function upb_saisi(): ?float
    {
        return $this->input->caracteristique()->upb?->valeur();
    }

    public function input(): PlancherBas
    {
        return $this->input;
    }

    public function context(): SimulationEngine
    {
        return $this->context;
    }

    public function __invoke(PlancherBas $input, SimulationEngine $context): self
    {
        $engine = clone $this;
        $engine->input = $input;
        $engine->context = $context;
        $engine->fetch();
        return $engine;
    }
}
