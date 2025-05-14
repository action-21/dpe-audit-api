<?php

namespace App\Engine\Performance\Chauffage\Perte;

use App\Domain\Audit\Audit;
use App\Domain\Chauffage\Entity\Generateur;
use App\Domain\Chauffage\Enum\UsageChauffage;
use App\Domain\Common\Enum\{Mois, ScenarioUsage, TypePerte, Usage};
use App\Domain\Common\ValueObject\Pertes;
use App\Engine\Performance\Apport\ApportEnveloppe;
use App\Engine\Performance\Deperdition\DeperditionEnveloppe;
use App\Engine\Performance\Chauffage\Dimensionnement\DimensionnementGenerateur;
use App\Engine\Performance\Chauffage\Performance\PerformanceGenerateur;
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
     * @see \App\Engine\Performance\Chauffage\Dimensionnement\DimensionnementGenerateur::pn()
     */
    public function pn(): float
    {
        return $this->generateur->data()->pn;
    }

    /**
     * @see \App\Engine\Performance\Chauffage\Performance\PerformanceGenerateurCombustion::qp0()
     */
    public function qp0(): float
    {
        return $this->generateur->data()->qp0 ?? 0;
    }

    /**
     * Besoin mensuel de chauffage hors pertes exprimé en kWh
     * 
     * @see \App\Engine\Performance\Chauffage\Performance\PerformanceGenerateurCombustion::qp0()
     */
    public function bch_hp(ScenarioUsage $scenario, Mois $mois): float
    {
        $bv = $this->gv() * (1 - $this->f(scenario: $scenario, mois: $mois));
        return $bv * $this->dh(scenario: $scenario, mois: $mois) / 1000;
    }

    /**
     * Pertes mensuelles de génération exprimées en Wh
     */
    public function pertes_generation(ScenarioUsage $scenario, Mois $mois): float
    {
        if (false === $this->generateur->type()->is_combustion()) {
            return 0;
        }

        $nref = $this->nref(scenario: $scenario, mois: $mois);
        $cper = $this->generateur->combustion()?->presence_ventouse ? 0.75 : 0.5;
        $qp0 = $this->qp0();
        $bch_hp = $this->bch_hp(scenario: $scenario, mois: $mois);
        $pn = $this->pn();
        $dper = min($nref, (1.3 * $bch_hp) / (0.3 / $pn));

        if ($this->generateur->usage() === UsageChauffage::CHAUFFAGE_ECS) {
            $dper = min($nref, (1.3 * $bch_hp) / (0.3 / $pn) + $nref * (1790 / 8760));
        }
        return $cper * $qp0 * $dper;
    }

    /**
     * Pertes mensuelles de génération de chauffage récupérables exprimées en Wh
     */
    public function pertes_generation_recuperables(ScenarioUsage $scenario, Mois $mois): float
    {
        return 0.48 * $this->pertes_generation(scenario: $scenario, mois: $mois);
    }

    public function apply(Audit $entity): void
    {
        $this->audit = $entity;

        foreach ($entity->chauffage()->generateurs() as $generateur) {
            $this->generateur = $generateur;
            $this->clear();

            $pertes = Pertes::create(
                usage: Usage::CHAUFFAGE,
                type: TypePerte::GENERATION,
                callback: fn(ScenarioUsage $scenario, Mois $mois) => $this->pertes_generation(
                    scenario: $scenario,
                    mois: $mois
                )
            );
            $pertes_recuperables = Pertes::create(
                usage: Usage::CHAUFFAGE,
                type: TypePerte::GENERATION,
                callback: fn(ScenarioUsage $scenario, Mois $mois) => $this->pertes_generation_recuperables(
                    scenario: $scenario,
                    mois: $mois
                )
            );
            $generateur->calcule($generateur->data()->with(
                pertes: $pertes,
                pertes_recuperables: $pertes_recuperables,
            ));
            $generateur->chauffage()->calcule($generateur->chauffage()->data()->with(
                pertes: $pertes,
                pertes_recuperables: $pertes_recuperables,
            ));
        }
    }

    public static function dependencies(): array
    {
        return [
            ScenarioClimatique::class,
            DeperditionEnveloppe::class,
            ApportEnveloppe::class,
            DimensionnementGenerateur::class,
            PerformanceGenerateur::class,
        ];
    }
}
