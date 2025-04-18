<?php

namespace App\Domain\Chauffage\Engine\Rendement\RendementGeneration;

use App\Domain\Chauffage\Engine\Performance\{PerformanceChaudiere, PerformancePac};
use App\Domain\Chauffage\Entity\Systeme;
use App\Domain\Chauffage\Enum\ModeCombustion;
use App\Domain\Chauffage\Enum\TypeGenerateur;
use App\Domain\Common\Enum\ScenarioUsage;

final class RendementChaudiere extends RendementCombustion
{
    use RendementPacHybride;

    /**
     * @see \App\Domain\Chauffage\Engine\Performance\PerformanceChaudiere::tfonc30()
     */
    public function tfonc30(): float
    {
        return $this->generateur()->data()->tfonc30;
    }

    /**
     * @see \App\Domain\Chauffage\Engine\Performance\PerformanceChaudiere::tfonc100()
     */
    public function tfonc100(): float
    {
        return $this->generateur()->data()->tfonc100;
    }

    /**
     * Présence d'une régulation
     */
    public function regulation(): bool
    {
        return $this->installation()->regulation_centrale()->presence_regulation
            || $this->installation()->regulation_terminale()->presence_regulation;
    }

    /**
     * @see \App\Domain\Chauffage\Engine\Performance\PerformancePac::scop()
     */
    public function rg(ScenarioUsage $scenario): float
    {
        if ($this->generateur()->type()->is_pac_hybride()) {
            $rg_partie_chaudiere = $this->rg_combustion($scenario) * self::taux_couverture_partie_chaudiere(
                zone_climatique: $this->audit->adresse()->zone_climatique,
            );
            $rg_partie_pac = $this->generateur()->data()->scop * self::taux_couverture_partie_pac(
                zone_climatique: $this->audit->adresse()->zone_climatique,
            );
            return $rg_partie_chaudiere + $rg_partie_pac;
        }
        return $this->rg_combustion($scenario);
    }

    public function qp(float $x): float
    {
        $mode_combustion = $this->generateur()->combustion()->mode_combustion;
        $regulation = $this->regulation();
        $qp0 = $this->qp0();
        $pn = $this->pn();
        $rpn = $this->rpn();
        $rpint = $this->rpint();
        $tfonc30 = $this->tfonc30();
        $tfonc100 = $this->tfonc100();

        if ($mode_combustion === ModeCombustion::STANDARD) {
            if ($x == 30) {
                $tfonc = $regulation ? $tfonc30 : $tfonc100;
                return 0.3 * $pn * ((100 - ($rpint + 0.1 * (50 - $tfonc))) / ($rpint + 0.1 * (50 - $tfonc)));
            }
            if ($x == 100) {
                $tfonc = $tfonc100;
                return $pn * ((100 - ($rpn + 0.1 * (70 - $tfonc))) / ($rpn + 0.1 * (70 - $tfonc)));
            }
            if ($x < 30) {
                $qp30 = $this->qp(30);
                return ((($qp30 - 0.15 * $qp0) * $x) / 0.3) + 0.15 * $qp0;
            }
            if ($x < 100) {
                $qp30 = $this->qp(30);
                $qp100 = $this->qp(100);
                return ((($qp100 - $qp30) * $x) / 0.7) + $qp30 - ((($qp100 - $qp30) * 0.3) / 0.7);
            }
        } else {
            if ($x == 15) {
                $qp30 = $this->qp(30);
                return $qp30 / 2;
            }
            if ($x == 30) {
                $tfonc = $regulation ? $tfonc30 : $tfonc100;
                return $mode_combustion === ModeCombustion::CONDENSATION
                    ? 0.3 * $pn * ((100 - ($rpint + 0.2 * (33 - $tfonc))) / ($rpint + 0.2 * (33 - $tfonc)))
                    : 0.3 * $pn * ((100 - ($rpint + 0.1 * (40 - $tfonc))) / ($rpint + 0.1 * (40 - $tfonc)));
            }
            if ($x == 100) {
                $tfonc = $tfonc100;
                return $pn * ((100 - ($rpn + 0.1 * (70 - $tfonc))) / ($rpn + 0.1 * (70 - $tfonc)));
            }
            if ($x < 15) {
                $QP15 = $this->qp(15);
                return ((($QP15 - 0.15 * $qp0) * $x) / 0.15) + 0.15 * $qp0;
            }
            if ($x < 30) {
                $QP15 = $this->qp(15);
                $qp30 = $this->qp(30);
                return ((($qp30 - $QP15) * $x) / 0.15) + $QP15 * ((($qp30 - $QP15) * 0.15) / 0.15);
            }
            if ($x < 100) {
                $qp30 = $this->qp(30);
                $qp100 = $this->qp(100);
                return ((($qp100 - $qp30) * $x) / 0.7) + $qp30 - ((($qp100 - $qp30) * 0.3) / 0.7);
            }
        }
        return 0;
    }

    public static function dependencies(): array
    {
        return parent::dependencies() + [PerformanceChaudiere::class, PerformancePac::class];
    }

    public static function supports(Systeme $systeme): bool
    {
        $type_generateur = $systeme->generateur()->type()->is_pac_hybride()
            ? TypeGenerateur::CHAUDIERE
            :  $systeme->generateur()->type();

        $energie_generateur =  $systeme->generateur()->type()->is_pac_hybride()
            ? $systeme->generateur()->energie_partie_chaudiere()
            : $systeme->generateur()->energie();

        return $type_generateur->is_chaudiere()
            && $energie_generateur->is_combustible()
            && false === $energie_generateur->is_bois()
            && false === $systeme->generateur()->position()->generateur_multi_batiment;
    }
}
