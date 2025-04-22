<?php

namespace App\Domain\Chauffage\Engine\Consommation;

use App\Domain\Audit\Audit;
use App\Domain\Audit\Engine\ScenarioClimatique;
use App\Domain\Chauffage\Engine\Besoin\BesoinChauffage;
use App\Domain\Chauffage\Engine\Dimensionnement\{DimensionnementAuxiliaire, DimensionnementGenerateur, DimensionnementInstallation, DimensionnementSysteme};
use App\Domain\Chauffage\Entity\{Emetteur, Systeme};
use App\Domain\Common\EngineRule;
use App\Domain\Common\Enum\{Energie, Mois, ScenarioUsage, Usage};
use App\Domain\Common\ValueObject\Consommations;
use App\Domain\Enveloppe\Engine\Deperdition\DeperditionEnveloppe;

final class ConsommationAuxiliaire extends EngineRule
{
    private Audit $audit;
    private Systeme $systeme;

    /**
     * @see \App\Domain\Audit\Engine\ScenarioClimatique::sollicitations_exterieures()
     */
    public function tbase(): float
    {
        return $this->audit->data()->tbase;
    }

    /**
     * @see \App\Domain\Audit\Engine\ScenarioClimatique::sollicitations_exterieures()
     */
    public function nref(ScenarioUsage $scenario, Mois $mois): float
    {
        return $this->audit->data()->sollicitations_exterieures->nref(scenario: $scenario, mois: $mois);
    }

    /**
     * @see \App\Domain\Enveloppe\Engine\Deperdition\DeperditionEnveloppe::gv()
     */
    public function gv(): float
    {
        return $this->audit->enveloppe()->data()->deperditions->get();
    }

    /**
     * @see \App\Domain\Chauffage\Engine\Besoin\BesoinChauffage::bch()
     */
    public function bch(ScenarioUsage $scenario): float
    {
        return $this->audit->chauffage()->data()->besoins->get($scenario);
    }

    /**
     * @see \App\Domain\Chauffage\Engine\Dimensionnement\DimensionnementGenerateur::pn()
     */
    public function pn(): float
    {
        return $this->systeme->generateur()->data()->pn;
    }

    /**
     * @see \App\Domain\Chauffage\Engine\Dimensionnement\DimensionnementAuxiliaire::paux()
     */
    public function paux(): float
    {
        return $this->systeme->generateur()->data()->paux;
    }

    /**
     * @see \App\Domain\Chauffage\Engine\Dimensionnement\DimensionnementInstallation::rdim()
     * @see \App\Domain\Chauffage\Engine\Dimensionnement\DimensionnementSysteme::rdim()
     */
    public function rdim(): float
    {
        return $this->systeme->installation()->data()->rdim * $this->systeme->data()->rdim;
    }

    /**
     * Consommation des auxiliaires exprimées en kWh
     */
    public function caux(ScenarioUsage $scenario): float
    {
        return $this->caux_generation(scenario: $scenario) + $this->caux_distribution(scenario: $scenario);
    }

    /**
     * Consommation des auxiliaires de génération exprimées en kWh
     */
    public function caux_generation(ScenarioUsage $scenario): float
    {
        $bch = $this->bch(scenario: $scenario);
        $rdim = $this->rdim();
        $paux = $this->paux();
        $pn = $this->pn();

        return ($paux / 1000 * $bch * $rdim) / $pn;
    }

    /**
     * Consommations des auxiliaires de génération exprimées en kWh
     */
    public function caux_distribution(ScenarioUsage $scenario): float
    {
        $puissance_circulateur = $this->puissance_circulateur();

        return Mois::reduce(
            fn(float $caux, Mois $mois) => $caux + $puissance_circulateur * $this->nref(scenario: $scenario, mois: $mois) / 1000
        );
    }

    /**
     * Puissance nominale en chaud expriomée en kW
     */
    public function pnc(): float
    {
        return pow(10, -3) * $this->gv() * (20 - $this->tbase());
    }

    /**
     * Puissance du circulateur exprimée en W
     */
    public function puissance_circulateur(): float
    {
        $debit_circulateur = $this->debit_circultateur();
        $pertes_charge = $this->pertes_charge();
        $surface = $this->systeme->installation()->surface();

        $puissance_circulateur = 6.44;
        $puissance_circulateur *= pow($pertes_charge * ($debit_circulateur / max(1, $surface / 400)), 0.676);
        $puissance_circulateur += max(1, $surface / 400);
        return \max(30, $puissance_circulateur);
    }

    /**
     * Débit nominal du circulateur exprimé en m³/h
     */
    public function debit_circultateur(): float
    {
        $chute_nominale_temperature = $this->chute_nominale_temperature();
        return  $chute_nominale_temperature
            ? ($this->pnc() * $this->rdim()) / (1.163 * $chute_nominale_temperature)
            : 0;
    }

    /**
     * Longueur du réseau de distribution exprimées en m
     */
    public function lem(): float
    {
        $fcot = $this->fcot();
        $niveaux_desservis = $this->systeme->reseau()->niveaux_desservis;
        $surface = $this->systeme->installation()->surface();

        return 5 * $fcot * ($niveaux_desservis + \pow($surface / $niveaux_desservis, 0.5));
    }

    /**
     * Chute de température du réseau de distribution exprimée en °C
     */
    public function chute_nominale_temperature(): float
    {
        return $this->systeme->emetteurs()->reduce(
            fn(float $carry, Emetteur $item) => max($carry, $item->temperature_distribution()->chute_nominale_temperature()),
        );
    }

    /**
     * Pertes de charge de l'émetteur exprimées en kPa
     */
    public function pertes_charge(): float
    {
        return $this->systeme->emetteurs()->reduce(
            fn(float $pertes, Emetteur $item) => $pertes + 0.15 * $this->lem() + $item->type()->pertes_charge()
        );
    }

    public function fcot(): float
    {
        return $this->systeme->emetteurs()->reduce(
            fn(float $fcot, Emetteur $item) => max($fcot, $item->type()->fcot()),
        );
    }

    public function apply(Audit $entity): void
    {
        $this->audit = $entity;

        if (0 === $entity->chauffage()->systemes()->count()) {
            $entity->chauffage()->calcule($entity->chauffage()->data()->with(
                consommations: Consommations::from()
            ));
        }
        foreach ($entity->chauffage()->systemes() as $systeme) {
            $this->systeme = $systeme;

            $consommations = Consommations::create(
                usage: Usage::AUXILIAIRE,
                energie: Energie::ELECTRICITE,
                callback: fn(ScenarioUsage $scenario) => $this->caux($scenario),
            );
            $systeme->calcule($systeme->data()->with(
                consommations: $consommations,
            ));
            $systeme->installation()->calcule($systeme->installation()->data()->with(
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
            ScenarioClimatique::class,
            DeperditionEnveloppe::class,
            BesoinChauffage::class,
            DimensionnementGenerateur::class,
            DimensionnementAuxiliaire::class,
            DimensionnementInstallation::class,
            DimensionnementSysteme::class,
        ];
    }
}
