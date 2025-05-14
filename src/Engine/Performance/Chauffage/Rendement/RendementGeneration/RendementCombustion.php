<?php

namespace App\Engine\Performance\Chauffage\Rendement\RendementGeneration;

use App\Domain\Chauffage\Entity\Systeme;
use App\Domain\Chauffage\Enum\TauxCharge;
use App\Domain\Common\Enum\ScenarioUsage;
use App\Engine\Performance\Chauffage\Dimensionnement\DimensionnementGenerateur;
use App\Engine\Performance\Chauffage\Performance\PerformanceGenerateurCombustion;
use App\Engine\Performance\Chauffage\Rendement\RendementGeneration;
use App\Engine\Performance\Deperdition\DeperditionEnveloppe;
use App\Engine\Performance\Scenario\ScenarioClimatique;

abstract class RendementCombustion extends RendementGeneration
{
    /**
     * @see \App\Engine\Performance\Scenario\ScenarioClimatique::tbase()
     */
    public function tbase(): float
    {
        return $this->audit->data()->tbase;
    }

    /**
     * @see \App\Engine\Performance\Deperdition\DeperditionEnveloppe::gv()
     */
    public function gv(): float
    {
        return $this->audit->enveloppe()->data()->deperditions->get();
    }

    /**
     * Pertes à l'arrêt exprimées en kW
     * 
     * @see \App\Engine\Performance\Chauffage\Performance\PerformanceGenerateurCombustion::qp0()
     */
    public function qp0(): float
    {
        return $this->generateur()->data()->qp0 / 1000;
    }

    /**
     * @see \App\Engine\Performance\Chauffage\Performance\PerformanceGenerateurCombustion::rpn()
     */
    public function rpn(): float
    {
        return $this->generateur()->data()->rpn->value();
    }

    /**
     * @see \App\Engine\Performance\Chauffage\Performance\PerformanceGenerateurCombustion::rpint()
     */
    public function rpint(): float
    {
        return $this->generateur()->data()->rpint->value();
    }

    /**
     * Puissance de la veilleuse exprimée en kW
     * 
     * @see \App\Engine\Performance\Chauffage\Performance\PerformanceGenerateurCombustion::pveilleuse()
     */
    public function pveilleuse(): float
    {
        return $this->generateur()->data()->pveilleuse / 1000;
    }

    /**
     * @see \App\Engine\Performance\Chauffage\Dimensionnement\DimensionnementGenerateur::pn()
     */
    public function pn(): float
    {
        return $this->generateur()->data()->pn;
    }

    /**
     * Sommes des puissances nominales des générateurs en cascade
     * 
     * @see \App\Engine\Performance\Chauffage\Dimensionnement\DimensionnementGenerateur::pn()
     */
    public function somme_pn(): float
    {
        $systemes = $this->installation()->systemes()
            ->with_type($this->systeme->type_chauffage())
            ->with_generateur_combustion(true);

        if (null === $this->priorite_cascade()) {
            return $systemes->with_cascade(false)->reduce(
                fn(float $pn, Systeme $item) => $pn + $item->generateur()->data()->pn,
            );
        }
        if (0 === $this->priorite_cascade()) {
            return $systemes
                ->with_cascade(true)
                ->with_priorite_cascade(false)
                ->reduce(
                    fn(float $pn, Systeme $item) => $pn + $item->generateur()->data()->pn,
                );
        }
        return $systemes
            ->with_cascade(true)
            ->with_priorite_cascade(true)
            ->reduce(
                fn(float $pn, Systeme $item) => $pn + $item->generateur()->data()->pn,
            );
    }

    public function priorite_cascade(): ?int
    {
        return $this->generateur()->signaletique()->priorite_cascade;
    }

    /**
     * Rendement de génération par combustion (PCI)
     */
    public function rg_combustion(ScenarioUsage $scenario): float
    {
        $pmfou = 0;
        $pmcons = 0;

        foreach (TauxCharge::cases() as $x) {
            $pmfou += $this->pmfou(scenario: $scenario, x: $x);
            $pmcons += $this->pmcons(scenario: $scenario, x: $x);
        }
        $rg_pcs = $pmfou / ($pmcons + 0.45 * $this->qp0() + $this->pveilleuse());
        return $rg_pcs * $this->generateur()->energie()->to()->coefficient_conversion_pcs();
    }

    /**
     * Puissance fournie par le générateur au point de fonctionnement x (kW)
     */
    public function pmfou(ScenarioUsage $scenario, TauxCharge $x): float
    {
        return $this->pn() * $this->tch_final(scenario: $scenario, x: $x)
            * $this->coeff_pond_final(scenario: $scenario, x: $x);
    }

    /**
     * Puissance consommée par le générateur au point de fonctionnement x (kW)
     */
    public function pmcons(ScenarioUsage $scenario, TauxCharge $x)
    {
        $qpx = $this->qp(scenario: $scenario, x: $x);
        $pmfou = $this->pmfou(scenario: $scenario, x: $x);
        $p = $this->pn() * $this->tch_final(scenario: $scenario, x: $x);
        return $pmfou * (($p + $qpx) / $p);
    }

    /**
     * Taux de charge au point de fonctionnement x
     */
    public function tch(TauxCharge $x): float
    {
        return $x->taux_charge();
    }

    /**
     * Taux de charge intermédiaire au point de fonctionnement x (%)
     * 
     * TODO: Vérifier la méthode applicable aux générateurs en cascade
     * @see https://github.com/action-21/reno-audit/discussions/44
     */
    public function tch_dim(ScenarioUsage $scenario, TauxCharge $x): float
    {
        $tch = $this->tch($x);

        if (null === $this->priorite_cascade()) {
            $cdim_ref = $this->cdim_ref($scenario);
            return $x === TauxCharge::TCH95 ? $tch : \min($tch / $cdim_ref, 1);
        }
        return $tch;
    }

    /**
     * Taux de charge final au point de fonctionnement x (%)
     */
    public function tch_final(ScenarioUsage $scenario, TauxCharge $x): float
    {
        $tch_dim = $this->tch_dim(scenario: $scenario, x: $x);

        if (null === $this->priorite_cascade()) {
            return $tch_dim;
        }
        $prel = $this->prel();
        $ctch = $this->ctch(scenario: $scenario, x: $x, prel: $prel);
        return \min($ctch / $prel, 1);
    }

    /**
     * Coefficient de pondération au point de fonctionnement x
     */
    public function coeff_pond(TauxCharge $x): float
    {
        return $x->coefficient_ponderation();
    }

    /**
     * Coefficient de pondération intermédiaire au point de fonctionnement x
     * 
     * TODO: Vérifier la méthode applicable aux générateurs en cascade
     * @see https://github.com/action-21/reno-audit/discussions/44
     */
    public function coeff_pond_dim(ScenarioUsage $scenario, TauxCharge $x): float
    {
        $coeff_pond = $this->coeff_pond($x);

        if (null === $this->priorite_cascade()) {
            return $coeff_pond;
        }
        $tch_dim = $this->tch_dim(scenario: $scenario, x: $x);
        $prel = $this->prel();
        $ctch = $this->ctch(scenario: $scenario, x: $x, prel: $prel);

        return $coeff_pond * ($ctch / $tch_dim);
    }

    /**
     * Coefficient de pondération final au point de fonctionnement x (%)
     */
    public function coeff_pond_final(ScenarioUsage $scenario, TauxCharge $x): float
    {
        $coeff_pond_dim = $this->coeff_pond_dim(scenario: $scenario, x: $x);

        if (null === $this->priorite_cascade()) {
            return $coeff_pond_dim;
        }
        return $coeff_pond_dim / \array_reduce(
            TauxCharge::cases(),
            fn(float $carry, TauxCharge $item): float => $carry += $this->coeff_pond_dim(
                scenario: $scenario,
                x: $item
            ),
            0
        );
    }

    /**
     * Coefficient de pondération permettant de prendre en compte les charges partielles
     */
    public function cdim_ref(ScenarioUsage $scenario): float
    {
        $tcons = $scenario === ScenarioUsage::CONVENTIONNEL ? 19 : 21;
        return (1000 * $this->somme_pn()) / ($this->gv() * ($tcons - $this->tbase()));
    }

    /**
     * Contribution du générateur en cascade au taux de charge au point de fonctionnement x (%)
     * 
     * @param float $prel - Puissance relative du générateur en cascade
     */
    public function ctch(ScenarioUsage $scenario, TauxCharge $x, float $prel): float
    {
        $tch_dim = $this->tch_dim(scenario: $scenario, x: $x);

        if (0 == $this->priorite_cascade()) {
            return $tch_dim * $prel;
        }
        if (1 == $this->priorite_cascade()) {
            return \min($prel, $tch_dim);
        }
        if (2 == $this->priorite_cascade()) {
            $prel_1 = 1 - $prel;
            $ctch_1 = \min($prel_1, $tch_dim);
            return \min($prel, $tch_dim - $ctch_1);
        }
        return 0;
    }

    /**
     * Puissance relative du générateur en cascade
     */
    public function prel(): float
    {
        return $this->pn() / $this->somme_pn();
    }

    /**
     * Pertes au point de fonctionnement x (kW)
     */
    abstract public function qp(ScenarioUsage $scenario, TauxCharge $x): float;

    public static function dependencies(): array
    {
        return parent::dependencies() + [
            ScenarioClimatique::class,
            DeperditionEnveloppe::class,
            PerformanceGenerateurCombustion::class,
            DimensionnementGenerateur::class,
        ];
    }
}
