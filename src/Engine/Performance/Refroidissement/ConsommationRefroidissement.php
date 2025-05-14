<?php

namespace App\Engine\Performance\Refroidissement;

use App\Domain\Audit\Audit;
use App\Domain\Common\Enum\{ScenarioUsage, Usage};
use App\Domain\Common\ValueObject\Consommations;
use App\Domain\Refroidissement\Entity\Systeme;
use App\Engine\Performance\Rule;

final class ConsommationRefroidissement extends Rule
{
    private Systeme $systeme;

    /**
     * @see \App\Engine\Performance\Refroidissement\BesoinRefroidissement::bfr()
     */
    public function bfr(ScenarioUsage $scenario): float
    {
        return $this->systeme->refroidissement()->data()->besoins->get($scenario);
    }

    /**
     * @see \App\Engine\Performance\Refroidissement\PerformanceGenerateur::eer()
     */
    public function eer(): float
    {
        return $this->systeme->generateur()->data()->eer;
    }

    /**
     * @see \App\Engine\Performance\Refroidissement\DimensionnementInstallation::rdim()
     * @see \App\Engine\Performance\Refroidissement\DimensionnementSysteme::rdim()
     */
    public function rdim(): float
    {
        return $this->systeme->data()->rdim * $this->systeme->installation()->data()->rdim;
    }

    /**
     * Consommation annuelle de refroidissement exprimée en kWh
     */
    public function cfr(ScenarioUsage $scenario): float
    {
        return 0.9 * ($this->bfr($scenario) / $this->eer()) * $this->rdim();
    }

    public function apply(Audit $entity): void
    {
        if (0 === $entity->refroidissement()->systemes()->count()) {
            $entity->refroidissement()->calcule($entity->refroidissement()->data()->with(
                consommations: Consommations::from()
            ));
        }
        foreach ($entity->refroidissement()->systemes() as $systeme) {
            $this->systeme = $systeme;

            $consommations = Consommations::create(
                usage: Usage::REFROIDISSEMENT,
                energie: $systeme->generateur()->energie()->to(),
                callback: fn(ScenarioUsage $scenario) => $this->cfr($scenario),
            );

            $systeme->calcule($systeme->data()->with(
                consommations: $consommations,
            ));
            $systeme->generateur()->calcule($systeme->generateur()->data()->with(
                consommations: $consommations,
            ));
            $systeme->installation()->calcule($systeme->installation()->data()->with(
                consommations: $consommations,
            ));
            $systeme->refroidissement()->calcule($systeme->refroidissement()->data()->with(
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
            BesoinRefroidissement::class,
            DimensionnementSysteme::class,
            DimensionnementInstallation::class,
            PerformanceGenerateur::class,
        ];
    }
}
