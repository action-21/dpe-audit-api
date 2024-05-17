<?php

namespace App\Domain\Ecs\Engine;

use App\Domain\Batiment\Enum\{TypeBatiment, ZoneClimatique};
use App\Domain\Common\Enum\Mois;
use App\Domain\Common\Error\{EngineTableError, EngineValeurError};
use App\Domain\Common\ExpressionResolver;
use App\Domain\Ecs\Entity\Generateur;
use App\Domain\Ecs\Enum\{BouclageReseau, TypeGenerateur, TypeInstallation, TypeStockage};
use App\Domain\Ecs\InstallationEcsEngine;
use App\Domain\Ecs\Table\{Combustion, CombustionRepository, Cop, CopRepository, Cr, CrRepository, Rd, RdRepository, Rg, RgRepository};

final class GenerateurEngine
{
    private InstallationEcsEngine $engine;
    private Generateur $input;

    private ?Combustion $table_combustion = null;
    private ?Cop $table_cop = null;
    private ?Cr $table_cr = null;
    private ?Rd $table_rd = null;
    private ?Rg $table_rg = null;

    public function __construct(
        private CombustionRepository $table_combustion_repository,
        private CopRepository $table_cop_repository,
        private CrRepository $table_cr_repository,
        private RdRepository $table_rd_repository,
        private RgRepository $table_rg_repository,
        private ExpressionResolver $expression_resolver,
    ) {
    }

    /**
     * Consommation du générateur en kWh PCI
     */
    public function cecs(bool $scenario_depensier = false): float
    {
        return $this->becs($scenario_depensier) * $this->iecs($scenario_depensier) * (1 - $this->engine->fecs()) * $this->rdim();
    }

    /**
     * Consommation du générateur pour le mois j en kWh PCI
     */
    public function cecs_j(Mois $mois, bool $scenario_depensier = false): float
    {
        return $this->becs_j($mois, $scenario_depensier) / $this->becs($scenario_depensier) * $this->cecs($scenario_depensier);
    }

    /**
     * Rendement du générateur d'ECS
     */
    public function iecs(bool $scenario_depensier = false): float
    {
        return 1 / ($this->rd() * $this->rs() * $this->rg($scenario_depensier) * $this->rgs($scenario_depensier));
    }

    /**
     * Rendement de génération
     */
    public function rg(bool $scenario_depensier = false): float
    {
        if ($this->calcul_effet_joule()) {
            return $this->table_rg()->rg;
        }
        if ($this->calcul_chauffe_eau_gaz_instantanne()) {
            $becs = $this->becs($scenario_depensier);
            return 1 / (1 / $this->rpn() + 1790 * ($this->qp0() / $becs) + 6970 * ($this->pveil() / $becs));
        }
        return 1;
    }

    /**
     * Rendement de génération et de stockage
     */
    public function rgs(bool $scenario_depensier = false): float
    {
        if ($this->calcul_cet()) {
            return $this->cop();
        }
        if ($this->calcul_combustion()) {
            $becs = $this->becs($scenario_depensier);
            return 1 / (1 / $this->rpn() + (1790 * $this->qp0() + $this->qgw()) / $becs + 6970 * (0.5 * $this->pveil() / $becs));
        }
        if ($this->calcul_accumulateur_gaz()) {
            $becs = $this->becs($scenario_depensier);
            return 1 / (1 / $this->rpn() + (8592 * $this->qp0() + $this->qgw()) / $becs + 6970 * ($this->pveil() / $becs));
        }
        if ($this->calcul_reseau_chaleur()) {
            return $this->type_generateur() === TypeGenerateur::RESEAU_CHALEUR_ISOLE ? 0.9 : 0.75;
        }
        return 1;
    }

    /**
     * Rendement de distribution de l'ECS
     */
    public function rd(): float
    {
        return $this->table_rd()->rd;
    }

    /**
     * Rendement de stockage
     */
    public function rs(): float
    {
        if ($this->type_stockage() === TypeStockage::SANS_STOCKAGE) {
            return 1;
        }
        if ($this->type_generateur() === TypeGenerateur::BALLON_ELECTRIQUE_ACCUMULATION_VERTICAL_CATEGORIE_C_OU_3_ETOILES) {
            return 1.08 / (1 + $this->qgw() * $this->rd() / $this->becs());
        }
        if ($this->type_generateur()->ballon_electrique()) {
            return 1 / (1 + $this->qgw() * $this->rd() / $this->becs());
        }
        return 1;
    }

    /**
     * Qg,w - Pertes de stockage en Wh
     */
    public function qgw(): float
    {
        if ($this->type_stockage() === TypeStockage::SANS_STOCKAGE) {
            return 0;
        }
        if ($this->calcul_cr()) {
            return 8592 * (45 / 24) * $this->volume_stockage() * $this->cr();
        }
        return 67662 * \pow($this->volume_stockage(), 0.55);
    }

    /**
     * cop - Coefficient de performance du chauffe-eau thermodynamique
     */
    public function cop(): ?float
    {
        if (false === $this->calcul_cet()) {
            return null;
        }
        if ($this->cop_saisi()) {
            return $this->cop_saisi();
        }
        if ($this->calcul_cet() && null === $this->table_cop) {
            throw new EngineTableError('ecs . générateur . cop');
        }
        return $this->table_cop()->cop;
    }

    /**
     * cr - Coefficient de perte du ballon de stockage en Wh/l.°C.jour
     */
    public function cr(): ?float
    {
        if (false === $this->calcul_cr()) {
            return null;
        }
        return $this->table_cr?->cr;
    }

    /**
     * Puissance conventionnelle d'ECS en kW
     */
    public function pecs(): float
    {
        if (!$this->volume_stockage()) {
            return 21;
        }
        if ($this->volume_stockage() <= 20) {
            return 21 - 0.8 * $this->volume_stockage();
        }
        if ($this->volume_stockage() <= 150) {
            return 5 - 1.751 * (($this->volume_stockage() - 20) / 65);
        }
        return (7.14 * $this->volume_stockage() + 428) / 1000;
    }

    /**
     * Pn - Puissance nominale retenue en kW PCI
     */
    public function pn(): ?float
    {
        if (false === $this->calcul_rpn() && false === $this->calcul_qp0()) {
            return null;
        }
        return $this->table_combustion()->pn_max ?? ($this->pn_saisi() ?? $this->pecs());
    }

    /**
     * Rpn - Rendement du générateur à combustion à pleine charge en %
     */
    public function rpn(): ?float
    {
        if (false === $this->calcul_rpn()) {
            return null;
        }
        if ($this->rpn_saisi()) {
            return $this->rpn_saisi();
        }
        if (false === $rpn = $this->expression_resolver->evalue($this->table_combustion()->qp0, ['Pn' => $this->pn()])) {
            throw new EngineValeurError('Rpn');
        }
        return $rpn;
    }

    /**
     * QP0 - Pertes à l'arrêt en kW PCI
     */
    public function qp0(): ?float
    {
        if (false === $this->calcul_qp0()) {
            return null;
        }
        if ($this->qp0_saisi()) {
            return $this->qp0_saisi();
        }
        if (!$qp0 = $this->expression_resolver->evalue($this->table_combustion()->qp0, ['Pn' => $this->pn(), 'E' => $this->e(), 'F' => $this->f(),])) {
            throw new EngineValeurError('qp0');
        }
        return $qp0;
    }

    /**
     * E - Coefficient de correction de l'efficacité de combustion
     */
    public function e(): ?float
    {
        if (false === $this->calcul_combustion()) {
            return null;
        }
        return $this->presence_ventouse() ? 1.75 : 2.5;
    }

    /**
     * F - Coefficient de correction de l'efficacité de combustion
     */
    public function f(): ?float
    {
        if (false === $this->calcul_combustion()) {
            return null;
        }
        return $this->presence_ventouse() ? -0.55 : -0.8;
    }

    /**
     * Puissance de la veilleuse en W PCI
     */
    public function pveil(): ?float
    {
        if (false === $this->calcul_combustion()) {
            return null;
        }
        if ($this->pveil_saisi()) {
            return $this->pveil_saisi();
        }
        return $this->table_combustion()->pveil ?? 0;
    }

    /**
     * Ratio de dimensionnement de l'installation d'ECS
     */
    public function rdim(): float
    {
        return ($count = $this->input()->installation()->generateur_collection()->count()) > 1 ? 1 / $count : 1;
    }

    /**
     * Scénario de calcul des chauffe-eau thermodynamiques
     */
    public function calcul_cet(): bool
    {
        return $this->type_generateur()->chauffe_eau_thermodynamique();
    }

    /**
     * Scénario de calcul des générateurs à effet joule
     */
    public function calcul_effet_joule(): bool
    {
        return $this->type_generateur()->chauffe_eau_electrique()
            || $this->type_generateur()->ballon_electrique()
            || $this->type_generateur()->chaudiere_electrique();
    }

    /**
     * Scénario de calcul des chauffe-eau gaz à production instantannée
     */
    public function calcul_chauffe_eau_gaz_instantanne(): bool
    {
        return $this->type_generateur()->chauffe_eau_gaz_instantanne();
    }

    /**
     * Scénario de calcul des accumulateurs gaz
     */
    public function calcul_accumulateur_gaz(): bool
    {
        return $this->type_generateur()->accumulateur_gaz();
    }

    /**
     * Scénario de calcul des réseaux de chaleur
     */
    public function calcul_reseau_chaleur(): bool
    {
        return $this->type_generateur()->reseau_chaleur() || $this->type_generateur()->multi_batiment();
    }

    /**
     * Scénario de calcul des générateurs à combustion
     */
    public function calcul_combustion(): bool
    {
        return $this->type_generateur()->generateur_combustion();
    }

    /**
     * Scénario de calcul des pertes à l'arrêt
     */
    public function calcul_qp0(): bool
    {
        return $this->calcul_combustion() || $this->calcul_accumulateur_gaz() || $this->calcul_chauffe_eau_gaz_instantanne();
    }

    /**
     * Scénario de calcul du rendement à pleine charge
     */
    public function calcul_rpn(): bool
    {
        return $this->calcul_combustion() || $this->calcul_accumulateur_gaz() || $this->calcul_chauffe_eau_gaz_instantanne();
    }

    /**
     * Scénario de calcul des ballons électriques
     */
    public function calcul_cr(): bool
    {
        return $this->type_generateur()->ballon_electrique();
    }

    /**
     * Valeur de la table ecs . générateur . combustion
     */
    public function table_combustion(): ?Combustion
    {
        if ($this->calcul_combustion() && null === $this->table_combustion) {
            throw new EngineTableError('ecs . générateur . combustion');
        }
        return $this->table_combustion;
    }

    /**
     * Valeur de la table ecs . générateur . cop
     */
    public function table_cop(): ?Cop
    {
        return $this->table_cop;
    }

    /**
     * Valeur de la table ecs . générateur . cr
     */
    public function table_cr(): ?Cr
    {
        if ($this->calcul_cr() && null === $this->table_cr) {
            throw new EngineTableError('ecs . générateur . cr');
        }
        return $this->table_cr;
    }

    /**
     * Valeur de la table ecs . générateur . rd
     */
    public function table_rd(): Rd
    {
        if (null === $this->table_rd) {
            throw new EngineTableError('ecs . générateur . rd');
        }
        return $this->table_rd;
    }

    /**
     * Valeur de la table ecs . générateur . rg
     */
    public function table_rg(): ?Rg
    {
        if ($this->calcul_effet_joule() && null === $this->table_rg()) {
            throw new EngineTableError('ecs . générateur . rg');
        }
        return $this->table_rg;
    }

    public function fetch(): void
    {
        $this->table_rd = $this->table_rd_repository->find_by(
            type_installation: $this->type_installation(),
            bouclage_reseau: $this->bouclage_reseau(),
            alimentation_contigue: $this->pieces_contigues(),
            position_volume_habitable: $this->position_volume_chauffe(),
        );

        $this->table_rg = $this->calcul_effet_joule() ? $this->table_rg_repository->find_by(
            type_generateur: $this->type_generateur(),
        ) : null;

        $this->table_cr = $this->calcul_cr() ? $this->table_cr_repository->find_by(
            type_generateur: $this->type_generateur(),
            volume_stockage: $this->volume_stockage() ?? 0,
        ) : null;

        $this->table_combustion = $this->calcul_combustion() ? $this->table_combustion_repository->find_by(
            type_generateur: $this->type_generateur(),
            annee_installation: $this->annee_installation() ?? 0,
            puissance_nominale: $this->pn_saisi() ?? 0,
        ) : null;

        $this->table_cop = $this->calcul_cet() ? $this->table_cop_repository->find_by(
            zone_climatique: $this->zone_climatique(),
            type_installation: $this->type_installation(),
            annee_installation: $this->annee_installation() ?? 0,
        ) : null;
    }

    // * Données d'entrée

    public function type_batiment(): TypeBatiment
    {
        return $this->input->installation()->logement()->batiment()->type_batiment();
    }

    public function zone_climatique(): ZoneClimatique
    {
        return $this->input->installation()->logement()->batiment()->adresse()->zone_climatique;
    }

    public function type_installation(): TypeInstallation
    {
        return $this->input->installation()->type_installation();
    }

    public function type_generateur(): TypeGenerateur
    {
        return $this->input->type_generateur();
    }

    public function bouclage_reseau(): BouclageReseau
    {
        return $this->input->installation()->bouclage_reseau();
    }

    public function type_stockage(): TypeStockage
    {
        return $this->input->stockage()->type_stockage;
    }

    public function pieces_contigues(): bool
    {
        return $this->input->installation()->pieces_contigues();
    }

    public function volume_stockage(): ?float
    {
        return $this->input->stockage()->volume_stockage?->valeur();
    }

    public function position_volume_chauffe(): bool
    {
        return $this->input->position_volume_chauffe();
    }

    public function position_volume_chauffe_stockage(): ?bool
    {
        return $this->input->stockage()->position_volume_chauffe;
    }

    public function presence_ventouse(): ?bool
    {
        return $this->input->performance()->presence_ventouse;
    }

    public function annee_installation(): ?int
    {
        return $this->input->annee_installation()?->valeur();
    }

    public function pn_saisi(): ?float
    {
        return $this->input->performance()->pn?->valeur();
    }

    public function pveil_saisi(): ?float
    {
        return $this->input->performance()->pveilleuse?->valeur();
    }

    public function qp0_saisi(): ?float
    {
        return $this->input->performance()->qp0?->valeur();
    }

    public function rpn_saisi(): ?float
    {
        return $this->input->performance()->rpn?->valeur();
    }

    public function cop_saisi(): ?float
    {
        return $this->input->performance()->cop?->valeur();
    }

    /**
     * @see \App\Domain\Batiment\Engine\Ecs
     */
    public function becs(bool $scenario_depensier = false): float
    {
        return $this->engine->context()->batiment_engine()->ecs()->becs($scenario_depensier);
    }

    /**
     * @see \App\Domain\Batiment\Engine\Ecs
     */
    public function becs_j(Mois $mois, bool $scenario_depensier = false): float
    {
        return $this->engine->context()->batiment_engine()->ecs()->becs_j($mois, $scenario_depensier);
    }

    public function input(): Generateur
    {
        return $this->input;
    }

    public function engine(): InstallationEcsEngine
    {
        return $this->engine;
    }

    public function __invoke(Generateur $input, InstallationEcsEngine $engine): self
    {
        $service = clone $this;
        $service->input = $input;
        $service->engine = $engine;
        $service->fetch();
        return $service;
    }
}
