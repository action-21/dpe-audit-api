<?php

namespace App\Engine\Performance\Ventilation;

use App\Domain\Audit\Audit;
use App\Domain\Common\Enum\{Energie, ScenarioUsage, Usage};
use App\Domain\Common\ValueObject\Consommations;
use App\Domain\Ventilation\Entity\Systeme;
use App\Engine\Performance\Rule;

final class ConsommationVentilation extends Rule
{
    private Systeme $systeme;

    /**
     * @see \App\Engine\Performance\Ventilation\DimensionnementInstallation::rdim()
     * @see \App\Engine\Performance\Ventilation\DimensionnementSysteme::rdim()
     */
    public function rdim(): float
    {
        return $this->systeme->data()->rdim * $this->systeme->installation()->data()->rdim;
    }

    /**
     * @see \App\Engine\Performance\Ventilation\PerformanceSysteme::pvent_moy()
     */
    public function pvent_moy(): float
    {
        return $this->systeme->data()->pvent_moy;
    }

    /**
     * @see \App\Engine\Performance\Ventilation\PerformanceSysteme::ratio_utilisation()
     */
    public function ratio_utilisation(): float
    {
        return $this->systeme->data()->ratio_utilisation;
    }

    /**
     * Consommation des auxiliaires de ventilation exprimée en kWh/an
     */
    public function caux(): float
    {
        $rdim = $this->rdim();
        $pvent_moy = $this->pvent_moy();
        $ratio_temps_utilisation = $this->ratio_utilisation();
        return 8760 * ($pvent_moy / 1000) * $rdim * $ratio_temps_utilisation;
    }

    public function apply(Audit $entity): void
    {
        if (0 === $entity->ventilation()->systemes()->count()) {
            $entity->ventilation()->calcule($entity->ventilation()->data()->with(
                consommations: Consommations::from()
            ));
        }
        foreach ($entity->ventilation()->systemes() as $systeme) {
            $this->systeme = $systeme;

            $consommations = Consommations::create(
                usage: Usage::AUXILIAIRE,
                energie: Energie::ELECTRICITE,
                callback: fn(ScenarioUsage $scenario) => $this->caux(),
            );

            $systeme->calcule($systeme->data()->with(
                consommations: $consommations
            ));
            $systeme->generateur()?->calcule($systeme->generateur()->data()->with(
                consommations: $consommations
            ));
            $systeme->installation()->calcule($systeme->installation()->data()->with(
                consommations: $consommations
            ));
            $systeme->ventilation()->calcule($systeme->ventilation()->data()->with(
                consommations: $consommations
            ));
            $entity->calcule($entity->data()->with(
                consommations: $consommations,
            ));
        }
    }

    public static function dependencies(): array
    {
        return [
            DimensionnementInstallation::class,
            DimensionnementSysteme::class,
            PerformanceSysteme::class,
        ];
    }
}
