<?php

namespace App\Domain\Ecs\Engine\Consommation;

use App\Domain\Audit\Audit;
use App\Domain\Common\EngineRule;
use App\Domain\Common\Enum\{ScenarioUsage, Usage};
use App\Domain\Common\ValueObject\Consommations;
use App\Domain\Ecs\Engine\Besoin\BesoinEcs;
use App\Domain\Ecs\Engine\Dimensionnement\{DimensionnementGenerateur, DimensionnementInstallation, DimensionnementSysteme};
use App\Domain\Ecs\Engine\Rendement\{RendementInstallation, RendementSysteme};
use App\Domain\Ecs\Entity\Systeme;

final class ConsommationEcs extends EngineRule
{
    private Systeme $systeme;

    /**
     * @see \App\Domain\Ecs\Engine\Besoin\BesoinEcs::becs_j()
     */
    public function becs(ScenarioUsage $scenario): float
    {
        return $this->systeme->ecs()->data()->besoins->get($scenario);
    }

    /**
     * @see \App\Domain\Ecs\Engine\Dimensionnement\DimensionnementSysteme::rdim()
     * @see \App\Domain\Ecs\Engine\Dimensionnement\DimensionnementInstallation::rdim()
     */
    public function rdim(): float
    {
        return $this->systeme->data()->rdim * $this->systeme->installation()->data()->rdim;
    }

    /**
     * @see \App\Domain\Ecs\Engine\Rendement\RendementSysteme::iecs()
     */
    public function iecs(ScenarioUsage $scenario): float
    {
        return $this->systeme->data()->rendements->iecs($scenario);
    }

    /**
     * @see \App\Domain\Ecs\Engine\Rendement\RendementInstallation::fecs()
     */
    public function fecs(): float
    {
        return $this->systeme->installation()->data()->fecs->decimal();
    }

    /**
     * Consommation annuelle d'eau chaude sanitaire exprimée en kWh
     */
    public function cecs(ScenarioUsage $scenario): float
    {
        $becs = $this->becs($scenario);
        $iecs = $this->iecs($scenario);
        $fecs = $this->fecs();
        $rdim = $this->rdim();

        return $becs * (1 - $fecs) * $iecs * $rdim;
    }

    public function apply(Audit $entity): void
    {
        if (0 === $entity->ecs()->systemes()->count()) {
            $entity->ecs()->calcule($entity->ecs()->data()->with(
                consommations: Consommations::from()
            ));
        }
        foreach ($entity->ecs()->systemes() as $systeme) {
            $this->systeme = $systeme;

            $consommations = Consommations::create(
                usage: Usage::ECS,
                energie: $systeme->generateur()->energie()->to(),
                callback: fn(ScenarioUsage $scenario) => $this->cecs($scenario),
            );

            $systeme->calcule($systeme->data()->with(
                consommations: $consommations,
            ));
            $systeme->installation()->calcule($systeme->installation()->data()->with(
                consommations: $consommations,
            ));
            $systeme->generateur()->calcule($systeme->generateur()->data()->with(
                consommations: $consommations,
            ));
            $systeme->ecs()->calcule($systeme->ecs()->data()->with(
                consommations: $consommations,
            ));
            $entity->calcule($entity->data()->with(
                consommations: $consommations,
            ));
        }
    }

    public static function dependencies(): array
    {
        return [
            ConsommationAuxiliaire::class,
            BesoinEcs::class,
            DimensionnementGenerateur::class,
            DimensionnementSysteme::class,
            DimensionnementInstallation::class,
            RendementSysteme::class,
            RendementInstallation::class,
        ];
    }
}
