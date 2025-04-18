<?php

namespace App\Domain\Ecs\Engine\Perte;

use App\Domain\Audit\Audit;
use App\Domain\Audit\Engine\ScenarioClimatique;
use App\Domain\Common\EngineRule;
use App\Domain\Common\Enum\{Mois, ScenarioUsage, TypePerte, Usage};
use App\Domain\Common\ValueObject\Pertes;
use App\Domain\Ecs\Engine\Besoin\BesoinEcs;
use App\Domain\Ecs\Engine\Dimensionnement\{DimensionnementInstallation, DimensionnementSysteme};
use App\Domain\Ecs\Entity\Systeme;

final class PerteDistribution extends EngineRule
{
    private Audit $audit;
    private Systeme $systeme;

    /**
     * @see \App\Domain\Audit\Engine\ScenarioClimatique::sollicitations_exterieures()
     */
    public function nref(ScenarioUsage $scenario, Mois $mois): float
    {
        return $this->audit->data()->sollicitations_exterieures->nref(scenario: $scenario, mois: $mois);
    }

    /**
     * @see \App\Domain\Ecs\Engine\Besoin\BesoinEcs::becs()
     */
    public function becs(ScenarioUsage $scenario, Mois $mois): float
    {
        return $this->systeme->ecs()->data()->besoins->get(scenario: $scenario, mois: $mois);
    }

    /**
     * @see \App\Domain\Ecs\Engine\DimensionnementInstallation::rdim()
     * @see \App\Domain\Ecs\Engine\DimensionnementSysteme::rdim()
     */
    public function rdim(): float
    {
        return $this->systeme->data()->rdim * $this->systeme->installation()->data()->rdim;
    }

    /**
     * Pertes mensuelles de distribution exprimées en Wh
     */
    public function pertes_distribution(ScenarioUsage $scenario, Mois $mois): float
    {
        $pertes = $this->pertes_distribution_ind_vc(scenario: $scenario, mois: $mois);
        $pertes += $this->pertes_distribution_col_vc(scenario: $scenario, mois: $mois);
        $pertes += $this->pertes_distribution_col_hvc(scenario: $scenario, mois: $mois);
        return $pertes;
    }

    /**
     * Pertes mensuelles de distribution individuelle en volume chauffé exprimées en Wh
     */
    public function pertes_distribution_ind_vc(ScenarioUsage $scenario, Mois $mois): float
    {
        $surface = $this->systeme->installation()->surface();
        $lvc = 0.2 * $surface * $this->rdim();
        return (0.5 * $lvc) / $surface * $this->becs(scenario: $scenario, mois: $mois) * 1000;
    }

    /**
     * Pertes mensuelles de distribution collective en volume chauffé exprimées en Wh
     */
    public function pertes_distribution_col_vc(ScenarioUsage $scenario, Mois $mois): float
    {
        return $this->systeme->generateur()->position()->generateur_collectif
            ? 0.112 * $this->becs(scenario: $scenario, mois: $mois) * 1000 * $this->rdim()
            : 0;
    }

    /**
     * Pertes mensuelles de distribution collective hors volume chauffé exprimées en Wh
     */
    public function pertes_distribution_col_hvc(ScenarioUsage $scenario, Mois $mois): float
    {
        return $this->systeme->generateur()->position()->generateur_collectif
            ? 0.028 * $this->becs(scenario: $scenario, mois: $mois) * 1000 * $this->rdim()
            : 0;
    }

    /**
     * Pertes mensuelles de distribution récupérables exprimées en Wh
     */
    public function pertes_distribution_recuperables(ScenarioUsage $scenario, Mois $mois): float
    {
        return 0.48 * $this->pertes_distribution(scenario: $scenario, mois: $mois) / 8760;
    }

    public function apply(Audit $entity): void
    {
        $this->audit = $entity;

        foreach ($entity->ecs()->systemes() as $systeme) {
            $this->systeme = $systeme;

            $pertes = Pertes::create(
                usage: Usage::ECS,
                type: TypePerte::DISTRIBUTION,
                callback: fn(ScenarioUsage $scenario, Mois $mois) => $this->pertes_distribution(
                    scenario: $scenario,
                    mois: $mois,
                ),
            );
            $pertes_recuperables = Pertes::create(
                usage: Usage::ECS,
                type: TypePerte::DISTRIBUTION,
                callback: fn(ScenarioUsage $scenario, Mois $mois) => $this->pertes_distribution_recuperables(
                    scenario: $scenario,
                    mois: $mois,
                ),
            );

            $systeme->calcule($systeme->data()->with(
                pertes: $pertes,
                pertes_recuperables: $pertes_recuperables,
            ));
            $systeme->installation()->calcule($systeme->installation()->data()->with(
                pertes: $pertes,
                pertes_recuperables: $pertes_recuperables,
            ));
            $systeme->ecs()->calcule($systeme->ecs()->data()->with(
                pertes: $pertes,
                pertes_recuperables: $pertes_recuperables,
            ));
        }
    }

    public static function dependencies(): array
    {
        return [
            ScenarioClimatique::class,
            BesoinEcs::class,
            DimensionnementInstallation::class,
            DimensionnementSysteme::class,
        ];
    }
}
