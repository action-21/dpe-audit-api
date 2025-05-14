<?php

namespace App\Engine\Performance\Chauffage\Besoin;

use App\Domain\Audit\Audit;
use App\Domain\Common\Enum\{Mois, ScenarioUsage, Usage};
use App\Domain\Common\ValueObject\Besoins;
use App\Engine\Performance\Apport\ApportEnveloppe;
use App\Engine\Performance\Chauffage\Perte\PerteGeneration;
use App\Engine\Performance\Ecs\Perte\PerteEcs;
use App\Engine\Performance\Deperdition\DeperditionEnveloppe;
use App\Engine\Performance\Rule;
use App\Engine\Performance\Scenario\ScenarioClimatique;

final class BesoinChauffage extends Rule
{
    private Audit $audit;

    /**
     * @see \App\Engine\Performance\Scenario\ScenarioClimatique::sollicitations_exterieures()
     */
    public function dh(ScenarioUsage $scenario, Mois $mois): float
    {
        return $this->audit->data()->sollicitations_exterieures->dh(scenario: $scenario, mois: $mois);
    }

    /**
     * @see \App\Engine\Performance\Deperdition\DeperditionEnveloppe::gv()
     */
    public function gv(): float
    {
        return $this->audit->enveloppe()->data()->deperditions->get();
    }

    /**
     * @see \App\Engine\Performance\Apport\ApportEnveloppe::f()
     */
    public function f(ScenarioUsage $scenario, Mois $mois): float
    {
        return $this->audit->enveloppe()->data()->apports->f(scenario: $scenario, mois: $mois);
    }

    /**
     * @see \App\Engine\Performance\Ecs\Perte\PerteEcs
     * @see \App\Engine\Performance\Chauffage\Perte\PerteGeneration::pertes_generation_recuperables()
     */
    public function pertes_recuperables(ScenarioUsage $scenario, Mois $mois): float
    {
        return $this->audit->ecs()->data()->pertes_recuperables->get(scenario: $scenario, mois: $mois)
            + $this->audit->chauffage()->data()->pertes_recuperables->get(scenario: $scenario, mois: $mois);
    }

    /**
     * Besoin mensuel de chauffage exprimé en kWh
     */
    public function bch(ScenarioUsage $scenario, Mois $mois): float
    {
        $bv = $this->gv() * (1 - $this->f(scenario: $scenario, mois: $mois));
        $bch = $bv * $this->dh(scenario: $scenario, mois: $mois) / 1000;
        $pertes_recuperables = min($bch, $this->pertes_recuperables(scenario: $scenario, mois: $mois) / 1000);
        return $bch - $pertes_recuperables;
    }

    public function apply(Audit $entity): void
    {
        $this->audit = $entity;

        $besoins = Besoins::create(
            usage: Usage::CHAUFFAGE,
            callback: fn(ScenarioUsage $scenario, Mois $mois) => $this->bch(
                scenario: $scenario,
                mois: $mois,
            ),
        );

        $entity->chauffage()->calcule($entity->chauffage()->data()->with(
            besoins: $besoins,
        ));
    }

    public static function dependencies(): array
    {
        return [
            ScenarioClimatique::class,
            DeperditionEnveloppe::class,
            ApportEnveloppe::class,
            PerteEcs::class,
            PerteGeneration::class,
        ];
    }
}
