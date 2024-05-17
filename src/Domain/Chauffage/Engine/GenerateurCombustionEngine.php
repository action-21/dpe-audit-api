<?php

namespace App\Domain\Chauffage\Engine;

use App\Domain\Common\Error\EngineTableError;
use App\Domain\Common\ExpressionResolver;
use App\Domain\Chauffage\Entity\Generateur;
use App\Domain\Chauffage\Enum\{TypeGenerateur, TypeStockage};
use App\Domain\Chauffage\Table\{Combustion, CombustionRepository};

/**
 * @see §14.1 - Générateurs à combustion
 */
final class GenerateurCombustionEngine
{
    private GenerateurEngine $engine;

    private ?Combustion $table_combustion = null;

    public function __construct(
        private CombustionRepository $table_combustion_repository,
        private ExpressionResolver $expression_resolver,
    ) {
    }

    /**
     * Rendement de génération du générateur à combustion
     */
    public function rg(bool $pcs = true): float
    {
        $rg = $this->pmfou() / ($this->pmcons() + 0.45 * $this->qp0() + $this->pveil());
        return $pcs ? $rg : $this->input()->energie()->coefficient_conversion_pcs() * $rg;
    }

    /**
     * coeff_pond,x - Coefficient de pondération au taux de charge x
     */
    public function coeff_pond_x(float $tch): float
    {
        return match (true) {
            $tch <= 0.1 => 0.1,
            $tch <= 0.2 => 0.25,
            $tch <= 0.3 => 0.2,
            $tch <= 0.4 => 0.15,
            $tch <= 0.5 => 0.1,
            $tch <= 0.6 => 0.1,
            $tch <= 0.7 => 0.05,
            $tch <= 0.8 => 0.025,
            $tch <= 0.9 => 0.025,
            default => 0,
        };
    }

    /**
     * Puissance conventionnelle d'ECS en kW
     */
    public function pecs(): float
    {
        if (!$this->input()->stockage()->volume_stockage?->valeur()) {
            return 21;
        }
        if ($this->input()->stockage()->volume_stockage->valeur() <= 20) {
            return 21 - 0.8 * $this->input()->stockage()->volume_stockage->valeur();
        }
        if ($this->input()->stockage()->volume_stockage->valeur() <= 150) {
            return 5 - 1.751 * (($this->input()->stockage()->volume_stockage->valeur() - 20) / 65);
        }
        if ($this->input()->stockage()->volume_stockage->valeur() > 150) {
            return (7.14 * $this->input()->stockage()->volume_stockage->valeur() + 428) / 1000;
        }
    }

    /**
     * Pn - Puissance nominale retenue en kW PCI
     */
    public function pn_max(): float
    {
        return $this->table_combustion()->pn_max ?? $this->input()->performance()->pn?->valeur();
    }

    /**
     * QP0 - Pertes à l'arrêt en kW PCI
     */
    public function qp0(): float
    {
        if ($this->input()->performance()->qp0) {
            return $this->input()->performance()->qp0->valeur();
        }
        return $this->expression_resolver->evalue($this->table_combustion()->qp0, [
            'Pn' => $this->pn_max(),
            'E' => $this->e(),
            'F' => $this->f(),
        ]);
    }

    /**
     * E - Coefficient de correction de l'efficacité de combustion
     */
    public function e(): float
    {
        return $this->input()->performance()->presence_ventouse ? 1.75 : 2.5;
    }

    /**
     * F - Coefficient de correction de l'efficacité de combustion
     */
    public function f(): float
    {
        return $this->input()->performance()->presence_ventouse ? -0.55 : -0.8;
    }

    /**
     * Puissance de la veilleuse en W PCI
     */
    public function pveil(): float
    {
        if ($this->input()->performance()->pveilleuse) {
            return $this->input()->performance()->pveilleuse->valeur();
        }
        return $this->table_combustion()->pveil ?? 0;
    }

    /**
     * Scénario de calcul des chauffe-eau gaz à production instantanée
     */
    public function calcul_chauffe_eau_gaz(): bool
    {
        return $this->input()->type_generateur() === TypeGenerateur::CHAUFFE_EAU_GAZ_PRODUCTION_INSTANTANEE;
    }

    /**
     * Scénario de calcul des accumulateurs gaz
     */
    public function calcul_accumulateur_gaz(): bool
    {
        return $this->input()->type_generateur()->accumulateur_gaz();
    }

    /**
     * Valeur de la table ecs . générateur . combustion
     */
    public function table_combustion(): Combustion
    {
        if (null === $this->table_combustion) {
            throw new EngineTableError('ecs . générateur . combustion');
        }
        return $this->table_combustion;
    }

    public function fetch(): void
    {
        $this->table_combustion = $this->table_combustion_repository->find_by(
            type_generateur: $this->input()->type_generateur(),
            annee_installation: $this->input()->annee_installation()?->valeur() ?? 0,
            puissance_nominale: $this->input()->performance()->pn?->valeur() ?? 0,
        );
    }

    public function input(): Generateur
    {
        return $this->engine->input();
    }

    public static function apply(Generateur $input): bool
    {
        return $input->type_generateur()->generateur_combustion();
    }

    public function __invoke(GenerateurEngine $engine): self
    {
        $service = clone $this;
        $service->engine = $engine;
        return $service;
    }
}
