<?php

namespace App\Engine\Performance\Ecs\Perte;

use App\Domain\Audit\Audit;
use App\Domain\Common\Enum\{Mois, ScenarioUsage, TypePerte, Usage};
use App\Domain\Common\ValueObject\Pertes;
use App\Domain\Ecs\Entity\Generateur;
use App\Domain\Ecs\Enum\UsageEcs;
use App\Engine\Performance\Ecs\Performance\PerformanceGenerateur;
use App\Engine\Performance\Rule;
use App\Engine\Performance\Scenario\ScenarioClimatique;

final class PerteGeneration extends Rule
{
    private Audit $audit;
    private Generateur $generateur;

    /**
     * @see \App\Engine\Performance\Scenario\ScenarioClimatique::sollicitations_exterieures()
     */
    public function nref(ScenarioUsage $scenario, Mois $mois): float
    {
        return $this->audit->data()->sollicitations_exterieures->nref(scenario: $scenario, mois: $mois);
    }

    /**
     * @see \App\Engine\Performance\Ecs\Performance\PerformanceGenerateurCombustion::qp0()
     */
    public function qp0(): float
    {
        return $this->generateur->data()->qp0;
    }

    /**
     * Pertes mensuelles de génération exprimées en Wh
     */
    public function pertes_generation(ScenarioUsage $scenario, Mois $mois): float
    {
        if (false === $this->generateur->type()->is_combustion()) {
            return 0;
        }
        // Cas des générateurs mixtes traités dans la partie chauffage.
        if ($this->generateur->usage() !== UsageEcs::ECS) {
            return 0;
        }

        $nref = $this->nref(scenario: $scenario, mois: $mois);
        $cper = $this->generateur->combustion()?->presence_ventouse ? 0.75 : 0.5;
        $dper = $nref * (1790 / 8760);
        $qp0 = $this->qp0();

        return $cper * $qp0 * $dper;
    }

    /**
     * Pertes annuelles de génération récupérables exprimées en Wh
     */
    public function pertes_generation_recuperables(ScenarioUsage $scenario, Mois $mois): float
    {
        return 0.48 * $this->pertes_generation(scenario: $scenario, mois: $mois);
    }

    public function apply(Audit $entity): void
    {
        $this->audit = $entity;

        foreach ($entity->ecs()->generateurs() as $generateur) {
            $this->generateur = $generateur;

            $pertes = Pertes::create(
                usage: Usage::ECS,
                type: TypePerte::GENERATION,
                callback: fn(ScenarioUsage $scenario, Mois $mois) => $this->pertes_generation(
                    scenario: $scenario,
                    mois: $mois,
                ),
            );
            $pertes_recuperables = Pertes::create(
                usage: Usage::ECS,
                type: TypePerte::GENERATION,
                callback: fn(ScenarioUsage $scenario, Mois $mois) => $this->pertes_generation_recuperables(
                    scenario: $scenario,
                    mois: $mois,
                ),
            );
            $generateur->calcule($generateur->data()->with(
                pertes: $pertes,
                pertes_recuperables: $pertes_recuperables,
            ));
            $generateur->ecs()->calcule($generateur->ecs()->data()->with(
                pertes: $pertes,
                pertes_recuperables: $pertes_recuperables,
            ));
        }
    }

    public static function dependencies(): array
    {
        return [
            ScenarioClimatique::class,
            PerformanceGenerateur::class,
        ];
    }
}
