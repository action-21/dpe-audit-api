<?php

namespace App\Engine\Performance\Chauffage\Rendement\RendementGeneration;

use App\Domain\Chauffage\Entity\Systeme;
use App\Domain\Chauffage\Enum\{ModeCombustion, TauxCharge};
use App\Domain\Common\Enum\ScenarioUsage;

final class RendementChaudiereCondensation extends RendementChaudiere
{
    public function qp(ScenarioUsage $scenario, TauxCharge $x): float
    {
        $QP0 = $this->qp0();
        $QP30 = $this->qp30();
        $QP100 = $this->qp100();
        $tch = $this->tch_final(scenario: $scenario, x: $x);

        if ($x === TauxCharge::TCH5) {
            $QP15 = $this->qp(scenario: $scenario, x: TauxCharge::TCH15);
            return ((($QP15 - 0.15 * $QP0) * $tch) / 0.15) + 0.15 * $QP0;
        }
        if ($x === TauxCharge::TCH15) {
            return $QP30 / 2;
        }
        if ($x === TauxCharge::TCH25) {
            $QP15 = $this->qp(scenario: $scenario, x: TauxCharge::TCH15);
            return ((($QP30 - $QP15) * $tch) / 0.15) + $QP15 * ((($QP30 - $QP15) * 0.15) / 0.15);
        }
        return ((($QP100 - $QP30) * $tch) / 0.7) + $QP30 - ((($QP100 - $QP30) * 0.3) / 0.7);
    }

    /**
     * Pertes de charge à 30% de puissance exprimées en kW
     */
    public function qp30(): float
    {
        $pn = $this->pn();
        $rpint = $this->rpint();
        $tfonc = $this->regulation() ? $this->tfonc30() : $this->tfonc100();

        $qp = 100 - ($rpint + 0.2 * (33 - $tfonc));
        $qp /= $rpint + 0.2 * (33 - $tfonc);
        $qp *= 0.3 * $pn;
        return $qp;
    }

    public static function match(Systeme $systeme): bool
    {
        return parent::match($systeme)
            && $systeme->generateur()->combustion()->mode_combustion === ModeCombustion::CONDENSATION
            && false === $systeme->generateur()->energie()->is_bois();
    }
}
