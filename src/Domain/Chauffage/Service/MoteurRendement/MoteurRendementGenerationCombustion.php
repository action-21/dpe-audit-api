<?php

namespace App\Domain\Chauffage\Service\MoteurRendement;

use App\Domain\Chauffage\Entity\Systeme;
use App\Domain\Chauffage\Enum\{CategorieGenerateur, EnergieGenerateur, TauxCharge};
use App\Domain\Common\Enum\ScenarioUsage;
use App\Domain\Simulation\Simulation;

/**
 * @property float $gv - Déperditions totales du bâtiment (W/K)
 * @property float $tbase - Température extéreure de base (°C)
 * @property ?int $priorite_cascade - Priorité de la cascade (null = Sans cascade, 0 = Sans prorité, 1 = Générateur principal, 2 = Générateur secondaire)
 * @property float $pn - Puissance nominale du générateur à combustion (kW)
 * @property float $somme_pn - Somme des puissances nominales des générateurs à combustion (kW)
 * @property float $qp0 - Pertes à l'arrêt du générateur (kW)
 * @property float $pveilleuse - Puissance de la veilleuse du générateur (kW)
 * @property float $rpn - Rendements à pleine charge du générateur (%)
 * @property ?float $rpint - Rendements à charge intermédiaire du générateur (%)
 * @property ?float $tfonc30 - Température de fonctionnement à 30% (°C)
 * @property ?float $tfonc100 - Température de fonctionnement à 100% (°C)
 */
final class MoteurRendementGenerationCombustion
{
    private ScenarioUsage $scenario;
    private CategorieGenerateur $categorie_generateur;
    private EnergieGenerateur $energie_generateur;

    private float $gv;
    private float $tbase;

    private bool $regulation;
    private ?int $priorite_cascade;
    private float $pn;
    private float $somme_pn;
    private float $qp0;
    private float $pveilleuse;
    private float $rpn;
    private ?float $rpint;
    private ?float $tfonc30;
    private ?float $tfonc100;

    public function initialise(
        ScenarioUsage $scenario,
        CategorieGenerateur $categorie_generateur,
        EnergieGenerateur $energie_generateur,
        bool $regulation,
        ?int $priorite_cascade,
        float $gv,
        float $tbase,
        float $pn,
        float $somme_pn,
        float $qp0,
        float $pveilleuse,
        float $rpn,
        ?float $rpint,
        ?float $tfonc30,
        ?float $tfonc100,
    ): self {
        $this->scenario = $scenario;
        $this->categorie_generateur = $categorie_generateur;
        $this->energie_generateur = $energie_generateur;
        $this->gv = $gv;
        $this->tbase = $tbase;
        $this->regulation = $regulation;
        $this->priorite_cascade = $priorite_cascade;
        $this->pn = $pn;
        $this->somme_pn = $somme_pn;
        $this->qp0 = $qp0;
        $this->pveilleuse = $pveilleuse;
        $this->rpn = $rpn;
        $this->rpint = $rpint;
        $this->tfonc30 = $tfonc30;
        $this->tfonc100 = $tfonc100;
        return $this;
    }

    /**
     * Rendement de génération par combustion (PCI)
     */
    public function calcule_rendement_generation(): float
    {
        $pmfou = 0;
        $pmcons = 0;

        foreach (TauxCharge::cases() as $tch) {
            $pmfou += $this->pmfou($tch->taux_charge());
            $pmcons += $this->pmcons($tch->taux_charge());
        }

        $rg_pcs = $pmfou / ($pmcons + 0.45 * $this->qp0 + $this->pveilleuse);
        return $rg_pcs * $this->energie_generateur->to()->coefficient_conversion_pcs();
    }

    /**
     * Puissance fournie par le générateur au point de fonctionnement x (kW)
     * 
     * @param float $x - Point de fonctionnement (%)
     */
    public function pmfou(float $x): float
    {
        return $this->pn * $this->tch_final($x) * $this->coeff_pond_final($x);
    }

    /**
     * Puissance consommée par le générateur au point de fonctionnement x (kW)
     * 
     * @param float $x - Point de fonctionnement (%)
     */
    public function pmcons(float $x)
    {
        $pmfou = $this->pmfou($x);
        $p = $this->pn * $this->tch_final($x) * $this->coeff_pond_final($x);
        return $pmfou  * (($p + $this->qp($x)) / $p);
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
    public function tch_dim(float $x): float
    {
        $tch = $this->tch($x);

        if (null === $this->priorite_cascade) {
            $classe = TauxCharge::classe($tch);
            $cdim_ref = $this->cdim_ref();
            return $classe === TauxCharge::TCH95 ? $tch : \min($tch / $cdim_ref, 1);
        }
        return $tch;
    }

    /**
     * Taux de charge final au point de fonctionnement x (%)
     * 
     * @param float $x - Point de fonctionnement (%)
     */
    public function tch_final(float $x): float
    {
        $tch_dim = $this->tch_dim($x);

        if (null === $this->priorite_cascade) {
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
    public function coeff_pond_dim(float $x): float
    {
        $coeff_pond = $this->coeff_pond($x);

        if (null === $this->priorite_cascade) {
            return $coeff_pond;
        }
        $tch_dim = $this->tch_dim($x);
        $prel = $this->prel();
        $ctch = $this->ctch(tch_dim: $tch_dim, prel: $prel);

        return $coeff_pond * ($ctch / $tch_dim);
    }

    /**
     * Coefficient de pondération final au point de fonctionnement x (%)
     * 
     * @param float $x - Point de fonctionnement (%)
     */
    public function coeff_pond_final(float $x): float
    {
        $coeff_pond_dim = $this->coeff_pond_dim($x);

        if (null === $this->priorite_cascade) {
            return $coeff_pond_dim;
        }
        return $coeff_pond_dim / \array_reduce(
            TauxCharge::cases(),
            fn(float $carry, TauxCharge $item): float => $carry += $this->coeff_pond_dim($item->taux_charge()),
            0
        );
    }

    /**
     * Coefficient de pondération permettant de prendre en compte les charges partielles
     */
    public function cdim_ref(): float
    {
        $tcons = $this->scenario === ScenarioUsage::CONVENTIONNEL ? 19 : 21;
        return $this->pn / ($this->gv * ($tcons - $this->tbase));
    }

    /**
     * Contribution du générateur en cascade au taux de charge au point de fonctionnement x (%)
     * 
     * @param float $tch - Taux de charge intermédiaire au point de fonctionnement x (%)
     * @param float $prel - Puissance relative du générateur en cascade
     */
    public function ctch(float $tch_dim, float $prel): float
    {
        if (0 === $this->priorite_cascade) {
            return $tch_dim * $prel;
        }
        if (1 === $this->priorite_cascade) {
            return \min($prel, $tch_dim);
        }
        if (2 === $this->priorite_cascade) {
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
        return $this->pn / $this->somme_pn;
    }

    /**
     * Pertes au point de fonctionnement x (kW)
     * 
     * @param float $x - Point de fonctionnement (%)
     */
    public function qp(float $x): float
    {
        $Pn = $this->pn;
        $QP0 = $this->qp0;
        $Rpn = $this->rpn;
        $Rpint = $this->rpint;

        if ($this->categorie_generateur === CategorieGenerateur::CHAUDIERE_STANDARD) {
            if ($x === 30) {
                $Tfonc = $this->regulation ? $this->tfonc30 : $this->tfonc100;
                return 0.3 * $Pn * ((100 - ($Rpint + 0.1 * (50 - $Tfonc))) / ($Rpint + 0.1 * (50 - $Tfonc)));
            }
            if ($x === 100) {
                $Tfonc = $this->tfonc100;
                return $Pn * ((100 - ($Rpn + 0.1 * (70 - $Tfonc))) / ($Rpn + 0.1 * (70 - $Tfonc)));
            }
            if ($x < 30) {
                $QP30 = $this->qp(30);
                return ((($QP30 - 0.15 * $QP0) * $x) / 0.3) + 0.15 * $QP0;
            }
            if ($x < 100) {
                $QP30 = $this->qp(30);
                $QP100 = $this->qp(100);
                return ((($QP100 - $QP30) * $x) / 0.7) + $QP30 - ((($QP100 - $QP30) * 0.3) / 0.7);
            }
        }
        if (\in_array($this->categorie_generateur, [
            CategorieGenerateur::CHAUDIERE_BASSE_TEMPERATURE,
            CategorieGenerateur::CHAUDIERE_CONDENSATION,
        ])) {
            if ($x === 15) {
                $QP30 = $this->qp(30);
                return $QP30 / 2;
            }
            if ($x === 30) {
                $Tfonc = $this->regulation ? $this->tfonc30 : $this->tfonc100;
                if ($this->categorie_generateur === CategorieGenerateur::CHAUDIERE_CONDENSATION) {
                    return 0.3 * $Pn * ((100 - ($Rpint + 0.2 * (33 - $Tfonc))) / ($Rpint + 0.2 * (33 - $Tfonc)));
                }
                return 0.3 * $Pn * ((100 - ($Rpint + 0.1 * (40 - $Tfonc))) / ($Rpint + 0.1 * (40 - $Tfonc)));
            }
            if ($x === 100) {
                $Tfonc = $this->tfonc100;
                return $Pn * ((100 - ($Rpn + 0.1 * (70 - $Tfonc))) / ($Rpn + 0.1 * (70 - $Tfonc)));
            }
            if ($x < 15) {
                $QP15 = $this->qp(15);
                return ((($QP15 - 0.15 * $QP0) * $x) / 0.15) + 0.15 * $QP0;
            }
            if ($x < 30) {
                $QP15 = $this->qp(15);
                $QP30 = $this->qp(30);
                return ((($QP30 - $QP15) * $x) / 0.15) + $QP15 * ((($QP30 - $QP15) * 0.15) / 0.15);
            }
            if ($x < 100) {
                $QP30 = $this->qp(30);
                $QP100 = $this->qp(100);
                return ((($QP100 - $QP30) * $x) / 0.7) + $QP30 - ((($QP100 - $QP30) * 0.3) / 0.7);
            }
        }
        if (\in_array($this->categorie_generateur, [
            CategorieGenerateur::CHAUDIERE_BOIS,
            CategorieGenerateur::POELE_BOIS_BOUILLEUR,
        ])) {
            if ($x === 50) {
                return 0.5 * $Pn * ((100 - $Rpint) / $Rpint);
            }
            if ($x === 100) {
                return $Pn * ((100 - $Rpn) / $Rpn);
            }
            if ($x < 50) {
                $QP50 = $this->qp(50);
                return ((($QP50 - 0.15 * $QP0) * $x) / 0.5) + 0.15 * $QP0;
            }
            if ($x < 100) {
                $QP50 = $this->qp(50);
                $QP100 = $this->qp(100);
                return ((($QP100 - $QP50) * $x) / 0.5) + 2 * $QP50 - $QP100;
            }
        }
        if ($this->categorie_generateur === CategorieGenerateur::GENERATEUR_AIR_CHAUD) {
            if ($x === 50) {
                return 0.5 * $Pn * ((100 - $Rpint) / $Rpint);
            }
            if ($x === 100) {
                return $Pn * ((100 - $Rpn) / $Rpn);
            }
            if ($x < 50) {
                $QP50 = $this->qp(50);
                return ((($QP50 - 0.15 * $QP0) * $x) / 0.5) + 0.15 * $QP0;
            }
            if ($x < 100) {
                $QP50 = $this->qp(50);
                $QP100 = $this->qp(100);
                return ((($QP100 - $QP50) * $x) / 0.5) + 2 * $QP50 - $QP100;
            }
        }
        if ($this->categorie_generateur === CategorieGenerateur::RADIATEUR_GAZ) {
            return 1.04 * ((100 - $Rpn) / $Rpn) * $Pn * $x;
        }
        return 0;
    }

    public function __invoke(Systeme $entity, Simulation $simulation, ScenarioUsage $scenario): self
    {
        $systemes = $entity->installation()->systemes()->filter_by_generateur_combustion();
        $priorite_cascade = $entity->generateur()->signaletique()?->priorite_cascade;
        $somme_pn = match (true) {
            (null === $priorite_cascade) => $systemes->filter_by_cascade(false)->pn(),
            (0 === $priorite_cascade) => $systemes->filter_by_cascade(true)->filter_by_priorite_cascade(false)->pn(),
            (0 < $priorite_cascade) =>  $systemes->filter_by_cascade(true)->filter_by_priorite_cascade(true)->pn(),
        };

        return $this->initialise(
            scenario: $scenario,
            categorie_generateur: $entity->generateur()->categorie(),
            energie_generateur: $entity->generateur()->energie(),
            regulation: $entity->installation()->regulation_centrale()->presence_regulation || $entity->installation()->regulation_terminale()->presence_regulation,
            priorite_cascade: $entity->generateur()->signaletique()?->priorite_cascade,
            gv: $simulation->enveloppe()->performance()->gv,
            tbase: $entity->chauffage()->audit()->situation()->tbase(),
            pn: $entity->generateur()->performance()->pn,
            somme_pn: $somme_pn,
            qp0: $entity->generateur()->performance()->qp0,
            pveilleuse: $entity->generateur()->performance()->pveilleuse,
            rpn: $entity->generateur()->performance()->rpn,
            rpint: $entity->generateur()->performance()->rpint,
            tfonc30: $entity->generateur()->performance()->tfonc30,
            tfonc100: $entity->generateur()->performance()->tfonc100,
        );
    }
}
