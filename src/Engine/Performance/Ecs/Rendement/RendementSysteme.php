<?php

namespace App\Engine\Performance\Ecs\Rendement;

use App\Domain\Audit\Audit;
use App\Domain\Common\Enum\{ScenarioUsage, TypePerte};
use App\Domain\Ecs\Entity\{Generateur, Systeme};
use App\Domain\Ecs\Service\EcsTableValeurRepository;
use App\Domain\Ecs\ValueObject\{Rendement, Rendements};
use App\Engine\Performance\Ecs\Performance\PerformanceGenerateur;
use App\Engine\Performance\Rule;

abstract class RendementSysteme extends Rule
{
    protected Audit $audit;
    protected Systeme $systeme;

    public function __construct(protected readonly EcsTableValeurRepository $table_repository) {}

    /**
     * @see \App\Engine\Performance\Ecs\BesoinEcs::becs()
     */
    public function becs(ScenarioUsage $scenario): float
    {
        return $this->systeme->ecs()->data()->besoins->get($scenario);
    }

    /**
     * @see \App\Engine\Performance\Ecs\Perte\PerteStockageIndependant::pertes_stockage()
     * @see \App\Engine\Performance\Ecs\Perte\PerteStockageIntegre::pertes_stockage()
     */
    public function pertes_stockage(ScenarioUsage $scenario): float
    {
        return $this->generateur()->data()->pertes->get(scenario: $scenario, type: TypePerte::STOCKAGE);
    }

    public function generateur(): Generateur
    {
        return $this->systeme->generateur();
    }

    /**
     * Production à l'intérieur du volume chauffé
     */
    public function production_volume_habitable(): bool
    {
        return $this->generateur()->position()->position_volume_chauffe;
    }

    /**
     * Réseau collectif
     */
    public function reseau_collectif(): bool
    {
        return $this->generateur()->position()->generateur_collectif;
    }

    /**
     * Inverse du rendement du système
     */
    public function iecs(ScenarioUsage $scenario): float
    {
        return 1 / array_product([
            $this->rg($scenario),
            $this->rgs($scenario),
            $this->rd($scenario),
            $this->rs($scenario),
        ]);
    }

    /**
     * Rendement annuel de distribution
     */
    final public function rd(ScenarioUsage $scenario): float
    {
        return $this->get("rd", function () {
            if (null === $rd = $this->table_repository->rd(
                production_volume_habitable: $this->production_volume_habitable(),
                reseau_collectif: $this->reseau_collectif(),
                bouclage_reseau: $this->systeme->reseau()->bouclage,
                alimentation_contigue: $this->systeme->reseau()->alimentation_contigue,
            )) {
                throw new \RuntimeException('Valeur forfaitaire Rd non trouvée');
            }
            return $rd;
        });
    }

    /**
     * Rendement annuel de stockage - Ne s'applique qu'aux systèmes électriques
     */
    public function rs(ScenarioUsage $scenario): float
    {
        return 1;
    }

    /**
     * Rendement annuel de génération/stockage
     */
    public function rgs(ScenarioUsage $scenario): float
    {
        return 1;
    }

    /**
     * Rendement annuel de génération
     */
    public function rg(ScenarioUsage $scenario): float
    {
        return 1;
    }

    abstract public static function match(Systeme $systeme): bool;

    public function apply(Audit $entity): void
    {
        $this->audit = $entity;

        foreach ($entity->ecs()->systemes() as $systeme) {
            if (static::match($systeme) === false) {
                continue;
            }
            $this->systeme = $systeme;
            $this->clear();

            $rendements = ScenarioUsage::each(fn(ScenarioUsage $scenario) => Rendement::create(
                scenario: $scenario,
                iecs: $this->iecs($scenario),
                rd: $this->rd($scenario),
                rgs: $this->rgs($scenario),
                rs: $this->rs($scenario),
                rg: $this->rg($scenario),
            ));
            $systeme->calcule($systeme->data()->with(
                rendements: Rendements::create(...$rendements),
            ));
        }
    }

    public static function dependencies(): array
    {
        return [PerformanceGenerateur::class];
    }
}
