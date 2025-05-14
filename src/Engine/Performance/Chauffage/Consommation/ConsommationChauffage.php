<?php

namespace App\Engine\Performance\Chauffage\Consommation;

use App\Domain\Audit\Audit;
use App\Domain\Common\Enum\{Mois, ScenarioUsage, Usage};
use App\Domain\Common\ValueObject\Consommations;
use App\Domain\Chauffage\Entity\Systeme;
use App\Domain\Chauffage\Enum\{ConfigurationSysteme as Configuration};
use App\Engine\Performance\Chauffage\Dimensionnement\{ConfigurationSysteme, DimensionnementGenerateur, DimensionnementInstallation, DimensionnementSysteme};
use App\Engine\Performance\Chauffage\Rendement\{RendementInstallation, RendementSysteme};
use App\Engine\Performance\Rule;

final class ConsommationChauffage extends Rule
{
    private Audit $audit;
    private Systeme $systeme;

    /**
     * @see \App\Engine\Performance\Scenario\ScenarioClimatique::sollicitations_exterieures()
     */
    public function dh14(Mois $mois): float
    {
        return $this->audit->data()->sollicitations_exterieures->dh14(mois: $mois);
    }

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
    public function text(Mois $mois): float
    {
        return $this->audit->data()->sollicitations_exterieures->text(mois: $mois);
    }

    /**
     * @see \App\Engine\Performance\Scenario\ScenarioClimatique::sollicitations_exterieures()
     */
    public function tbase(): float
    {
        return $this->audit->data()->tbase;
    }

    /**
     * @see \App\Engine\Performance\Chauffage\Besoin\BesoinChauffage::bch()
     */
    public function bch(ScenarioUsage $scenario): float
    {
        if ($this->systeme->installation()->systemes()->has_systeme_central_collectif()) {
            if (in_array($this->configuration(), [Configuration::BASE, Configuration::RELEVE])) {
                return Mois::reduce(function (float $bch, Mois $mois) use ($scenario) {
                    $bch_base = $this->systeme->chauffage()->data()->besoins->get(
                        scenario: $scenario,
                        mois: $mois
                    );
                    $dht = $this->dht(scenario: $scenario, mois: $mois);
                    $dh14 = $this->dh14(mois: $mois);
                    return $bch + $bch_base * (1 / ($dht / $dh14));
                });
            } else {
                return Mois::reduce(function (float $bch, Mois $mois) use ($scenario) {
                    $bch_base = $this->systeme->chauffage()->data()->besoins->get(
                        scenario: $scenario,
                        mois: $mois
                    );
                    $dht = $this->dht(scenario: $scenario, mois: $mois);
                    $dh14 = $this->dh14(mois: $mois);
                    return $bch + $bch_base * ($dht / $dh14);
                });
            }
        }
        return $this->systeme->chauffage()->data()->besoins->get($scenario);
    }

    /**
     * @see \App\Engine\Performance\Chauffage\Dimensionnement\ConfigurationSysteme::configuration()
     */
    public function configuration(): Configuration
    {
        return $this->systeme->data()->configuration;
    }

    /**
     * @see \App\Engine\Performance\Chauffage\Dimensionnement\DimensionnementSysteme::rdim()
     * @see \App\Engine\Performance\Chauffage\Dimensionnement\DimensionnementInstallation::rdim()
     */
    public function rdim(): float
    {
        return $this->systeme->data()->rdim * $this->systeme->installation()->data()->rdim;
    }

    /**
     * @see \App\Engine\Performance\Chauffage\Rendement\RendementSysteme::ich()
     */
    public function ich(ScenarioUsage $scenario): float
    {
        return $this->systeme->data()->ich->get($scenario);
    }

    /**
     * @see \App\Engine\Performance\Chauffage\Rendement\RendementInstallation::fch()
     */
    public function fch(): float
    {
        return $this->systeme->installation()->data()->fch->decimal();
    }

    /**
     * @see \App\Engine\Performance\Chauffage\Dimensionnement\DimensionnementGenerateur::pn()
     */
    public function pn(): float
    {
        return $this->systeme->generateur()->data()->pn;
    }

    /**
     * @see \App\Engine\Performance\Chauffage\Rendement\RendementDistribution::rd()
     */
    public function rd(): float
    {
        return $this->systeme->data()->rd;
    }

    /**
     * @see \App\Engine\Performance\Chauffage\Rendement\RendementEmission::re()
     */
    public function re(): float
    {
        return $this->systeme->data()->re;
    }

    /**
     * @see \App\Engine\Performance\Chauffage\Rendement\RendementRegulation::rr()
     */
    public function rr(): float
    {
        return $this->systeme->data()->rr;
    }

    /**
     * Puissance émise utile par le générateur en base exprimée en kW
     */
    public function pe(): float
    {
        return $this->pn() * $this->rd() * $this->re() * $this->rr();
    }

    /**
     * Température de dimensionnement
     */
    public function t(ScenarioUsage $scenario): float
    {
        $dh14 = Mois::reduce(fn(float $dh14, Mois $mois) => $dh14 + $this->dh14($mois));
        return 14 - ($this->pe() * $dh14 / $this->bch($scenario));
    }

    /**
     * Degré heure base T
     */
    public function dht(ScenarioUsage $scenario, Mois $mois): float
    {
        $nref = $this->nref(scenario: $scenario, mois: $mois);
        $text = $this->text(mois: $mois);
        $tbase = $this->tbase();
        $t = $this->t(scenario: $scenario);
        $x = 0.5 * (($t - $tbase) / ($text - $tbase));

        return $nref * ($text - $tbase) * pow($x, 5) * (14 - 25 * $x + 20 * pow($x, 2) - 5 * pow($x, 3));
    }

    /**
     * Consommation annuelle de chauffage exprimée en kWh
     */
    public function cch(ScenarioUsage $scenario): float
    {
        $bch = $this->bch($scenario);
        $ich = $this->ich($scenario);
        $fch = $this->fch();
        $rdim = $this->rdim();

        return $bch * (1 - $fch) * $ich * $rdim;
    }

    public function apply(Audit $entity): void
    {
        if (0 === $entity->chauffage()->systemes()->count()) {
            $entity->chauffage()->calcule($entity->chauffage()->data()->with(
                consommations: Consommations::from()
            ));
        }
        foreach ($entity->chauffage()->systemes() as $systeme) {
            $this->systeme = $systeme;

            $consommations = Consommations::create(
                usage: Usage::CHAUFFAGE,
                energie: $systeme->generateur()->energie()->to(),
                callback: fn(ScenarioUsage $scenario) => $this->cch($scenario),
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
            $systeme->chauffage()->calcule($systeme->chauffage()->data()->with(
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
            ConfigurationSysteme::class,
            DimensionnementGenerateur::class,
            DimensionnementSysteme::class,
            DimensionnementInstallation::class,
            RendementSysteme::class,
            RendementInstallation::class,
        ];
    }
}
