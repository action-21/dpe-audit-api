<?php

namespace App\Domain\Chauffage\Engine\Rendement\RendementGeneration;

use App\Domain\Audit\Engine\ScenarioClimatique;
use App\Domain\Chauffage\Engine\Dimensionnement\DimensionnementGenerateur;
use App\Domain\Chauffage\Engine\Performance\PerformanceGenerateurCombustion;
use App\Domain\Chauffage\Engine\Rendement\RendementGeneration;
use App\Domain\Chauffage\Entity\Systeme;
use App\Domain\Chauffage\Enum\TauxCharge;
use App\Domain\Common\Enum\ScenarioUsage;
use App\Domain\Enveloppe\Engine\Deperdition\DeperditionEnveloppe;

abstract class RendementCombustion extends RendementGeneration
{
    /**
     * @see \App\Domain\Audit\Engine\ScenarioClimatique::tbase()
     */
    public function tbase(): float
    {
        return $this->audit->data()->tbase;
    }

    /**
     * @see \App\Domain\Enveloppe\Engine\Deperdition\DeperditionEnveloppe::gv()
     */
    public function gv(): float
    {
        return $this->audit->enveloppe()->data()->deperditions->get();
    }

    /**
     * @see \App\Domain\Chauffage\Engine\Performance\PerformanceGenerateurCombustion::qp0()
     */
    public function qp0(): float
    {
        return $this->generateur()->data()->qp0;
    }

    /**
     * @see \App\Domain\Chauffage\Engine\Performance\PerformanceGenerateurCombustion::rpn()
     */
    public function rpn(): float
    {
        return $this->generateur()->data()->rpn->decimal();
    }

    /**
     * @see \App\Domain\Chauffage\Engine\Performance\PerformanceGenerateurCombustion::rpint()
     */
    public function rpint(): float
    {
        return $this->generateur()->data()->rpint->decimal();
    }

    /**
     * @see \App\Domain\Chauffage\Engine\Performance\PerformanceGenerateurCombustion::pveilleuse()
     */
    public function pveilleuse(): float
    {
        return $this->generateur()->data()->pveilleuse;
    }

    /**
     * @see \App\Domain\Chauffage\Engine\Dimensionnement\DimensionnementGenerateur::pn()
     */
    public function pn(): float
    {
        return $this->generateur()->data()->pn;
    }

    /**
     * Sommes des puissances nominales des générateurs en cascade
     * 
     * @see \App\Domain\Chauffage\Engine\Dimensionnement\DimensionnementGenerateur::pn()
     */
    public function somme_pn(): float
    {
        if (null === $this->priorite_cascade()) {
            return $this->installation()->systemes()
                ->with_cascade(false)
                ->reduce(
                    fn(float $pn, Systeme $item) => $pn + $item->generateur()->data()->pn,
                );
        }
        if (0 === $this->priorite_cascade()) {
            return $this->installation()->systemes()
                ->with_cascade(true)
                ->with_priorite_cascade(false)
                ->reduce(
                    fn(float $pn, Systeme $item) => $pn + $item->generateur()->data()->pn,
                );
        }
        return $this->installation()->systemes()
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

        foreach (TauxCharge::cases() as $tch) {
            $pmfou += $this->pmfou(scenario: $scenario, x: $tch->taux_charge());
            $pmcons += $this->pmcons(scenario: $scenario, x: $tch->taux_charge());
        }
        $rg_pcs = $pmfou / ($pmcons + 0.45 * $this->qp0() + $this->pveilleuse());
        return $rg_pcs * $this->generateur()->energie()->to()->coefficient_conversion_pcs();
    }

    /**
     * Puissance fournie par le générateur au point de fonctionnement x (kW)
     * 
     * @param float $x - Point de fonctionnement (%)
     */
    public function pmfou(ScenarioUsage $scenario, float $x): float
    {
        return $this->pn() * $this->tch_final(scenario: $scenario, x: $x) * $this->coeff_pond_final(scenario: $scenario, x: $x);
    }

    /**
     * Puissance consommée par le générateur au point de fonctionnement x (kW)
     * 
     * @param float $x - Point de fonctionnement (%)
     */
    public function pmcons(ScenarioUsage $scenario, float $x)
    {
        $pmfou = $this->pmfou(scenario: $scenario, x: $x);
        $p = $this->pn() * $this->tch_final(scenario: $scenario, x: $x);

        return $pmfou * (($p + $this->qp($x)) / $p);
    }

    /**
     * Taux de charge au point de fonctionnement x (%)
     * 
     * @param float $x - Point de fonctionnement (%)
     */
    public function tch(float $x): float
    {
        return (TauxCharge::classe($x))->taux_charge();
    }

    /**
     * Taux de charge intermédiaire au point de fonctionnement x (%)
     * 
     * TODO: Vérifier la méthode applicable aux générateurs en cascade
     * @see https://github.com/action-21/reno-audit/discussions/44
     * 
     * @param float $x - Point de fonctionnement (%)
     */
    public function tch_dim(ScenarioUsage $scenario, float $x): float
    {
        $tch = $this->tch($x);

        if (null === $this->priorite_cascade()) {
            $classe = TauxCharge::classe($tch);
            $cdim_ref = $this->cdim_ref($scenario);
            return $classe === TauxCharge::TCH95 ? $tch : \min($tch / $cdim_ref, 1);
        }
        return $tch;
    }

    /**
     * Taux de charge final au point de fonctionnement x (%)
     * 
     * @param float $x - Point de fonctionnement (%)
     */
    public function tch_final(ScenarioUsage $scenario, float $x): float
    {
        $tch_dim = $this->tch_dim(scenario: $scenario, x: $x);

        if (null === $this->priorite_cascade()) {
            return $tch_dim;
        }
        $prel = $this->prel();
        $ctch = $this->ctch(tch_dim: $tch_dim, prel: $prel);
        return \min($ctch / $prel, 1);
    }

    /**
     * Coefficient de pondération au point de fonctionnement x
     * 
     * @param float $x - Point de fonctionnement (%)
     */
    public function coeff_pond(float $x): float
    {
        return (TauxCharge::classe($x))->coefficient_ponderation();
    }

    /**
     * Coefficient de pondération intermédiaire au point de fonctionnement x
     * 
     * TODO: Vérifier la méthode applicable aux générateurs en cascade
     * @see https://github.com/action-21/reno-audit/discussions/44
     * 
     * @param float $x - Point de fonctionnement (%)
     */
    public function coeff_pond_dim(ScenarioUsage $scenario, float $x): float
    {
        $coeff_pond = $this->coeff_pond($x);

        if (null === $this->priorite_cascade()) {
            return $coeff_pond;
        }
        $tch_dim = $this->tch_dim(scenario: $scenario, x: $x);
        $prel = $this->prel();
        $ctch = $this->ctch(tch_dim: $tch_dim, prel: $prel);

        return $coeff_pond * ($ctch / $tch_dim);
    }

    /**
     * Coefficient de pondération final au point de fonctionnement x (%)
     * 
     * @param float $x - Point de fonctionnement (%)
     */
    public function coeff_pond_final(ScenarioUsage $scenario, float $x): float
    {
        $coeff_pond_dim = $this->coeff_pond_dim(scenario: $scenario, x: $x);

        if (null === $this->priorite_cascade()) {
            return $coeff_pond_dim;
        }
        return $coeff_pond_dim / \array_reduce(
            TauxCharge::cases(),
            fn(float $carry, TauxCharge $item): float => $carry += $this->coeff_pond_dim(
                scenario: $scenario,
                x: $item->taux_charge()
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
        return $this->pn() / ($this->gv() * ($tcons - $this->tbase()));
    }

    /**
     * Contribution du générateur en cascade au taux de charge au point de fonctionnement x (%)
     * 
     * @param float $tch - Taux de charge intermédiaire au point de fonctionnement x (%)
     * @param float $prel - Puissance relative du générateur en cascade
     */
    public function ctch(float $tch_dim, float $prel): float
    {
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
     * 
     * @param float $x - Point de fonctionnement (%)
     */
    abstract public function qp(float $x): float;

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
