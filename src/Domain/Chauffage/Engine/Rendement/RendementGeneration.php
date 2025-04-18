<?php

namespace App\Domain\Chauffage\Engine\Rendement;

use App\Domain\Audit\Audit;
use App\Domain\Chauffage\Engine\Besoin\BesoinChauffage;
use App\Domain\Chauffage\Entity\{Generateur, Installation, Systeme};
use App\Domain\Chauffage\Service\ChauffageTableValeurRepository;
use App\Domain\Chauffage\ValueObject\{Rendement, Rendements};
use App\Domain\Common\EngineRule;
use App\Domain\Common\Enum\ScenarioUsage;

abstract class RendementGeneration extends EngineRule
{
    protected Audit $audit;
    protected Systeme $systeme;

    public function __construct(protected readonly ChauffageTableValeurRepository $table_repository) {}

    /**
     * @see \App\Domain\Chauffage\Engine\Besoin\BesoinChauffage::bch()
     */
    public function bch(ScenarioUsage $scenario): float
    {
        return $this->systeme->chauffage()->data()->besoins->get($scenario);
    }

    public function generateur(): Generateur
    {
        return $this->systeme->generateur();
    }

    public function installation(): Installation
    {
        return $this->systeme->installation();
    }

    /**
     * Rendement de génération
     */
    abstract public function rg(ScenarioUsage $scenario): float;

    abstract public static function supports(Systeme $systeme): bool;

    final public function apply(Audit $entity): void
    {
        $this->audit = $entity;

        foreach ($entity->chauffage()->systemes() as $systeme) {
            if (false === static::supports($systeme)) {
                continue;
            }
            $this->systeme = $systeme;

            $rendements = ScenarioUsage::each(fn(ScenarioUsage $scenario) => Rendement::create(
                scenario: $scenario,
                rendement: $this->rg($scenario),
            ));

            $systeme->calcule($systeme->data()->with(
                rg: Rendements::create(...$rendements),
            ));
        }
    }

    public static function dependencies(): array
    {
        return [
            BesoinChauffage::class,
        ];
    }
}
