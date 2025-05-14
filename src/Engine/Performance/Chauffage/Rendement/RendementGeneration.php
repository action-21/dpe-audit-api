<?php

namespace App\Engine\Performance\Chauffage\Rendement;

use App\Domain\Audit\Audit;
use App\Domain\Chauffage\Entity\{Generateur, Installation, Systeme};
use App\Domain\Chauffage\Service\ChauffageTableValeurRepository;
use App\Domain\Chauffage\ValueObject\{Rendement, Rendements};
use App\Domain\Common\Enum\ScenarioUsage;
use App\Engine\Performance\Chauffage\Besoin\BesoinChauffage;
use App\Engine\Performance\Rule;

abstract class RendementGeneration extends Rule
{
    protected Audit $audit;
    protected Systeme $systeme;

    public function __construct(protected readonly ChauffageTableValeurRepository $table_repository) {}

    /**
     * @see \App\Engine\Performance\Chauffage\Besoin\BesoinChauffage::bch()
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

    abstract public static function match(Systeme $systeme): bool;

    final public function apply(Audit $entity): void
    {
        $this->audit = $entity;

        foreach ($entity->chauffage()->systemes() as $systeme) {
            if (false === static::match($systeme)) {
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
