<?php

namespace App\Engine\Performance\Chauffage\Rendement\RendementGeneration;

use App\Domain\Chauffage\Entity\Systeme;
use App\Domain\Chauffage\Enum\TauxCharge;
use App\Domain\Common\Enum\ScenarioUsage;

final class RendementGenerateurAirChaud extends RendementCombustion
{
    public function rg(ScenarioUsage $scenario): float
    {
        return $this->rg_combustion($scenario);
    }

    public function qp(ScenarioUsage $scenario, TauxCharge $x): float
    {
        $QP0 = $this->qp0();
        $QP50 = $this->qp50();
        $QP100 = $this->qp100();
        $tch = $this->tch_final(scenario: $scenario, x: $x);

        return $x->value < 50
            ? ((($QP50 - 0.15 * $QP0) * $tch) / 0.5) + 0.15 * $QP0
            : ((($QP100 - $QP50) * $tch) / 0.5) + 2 * $QP50 - $QP100;
    }

    /**
     * Pertes de charge à 50% de puissance
     */
    public function qp50(): float
    {
        $pn = $this->pn();
        $rpint = $this->rpint();
        return 0.5 * $pn * ((100 - $rpint) / $rpint);
    }

    /**
     * Pertes de charge à 100% de puissance
     */
    public function qp100(): float
    {
        $pn = $this->pn();
        $rpn = $this->rpn();
        return $pn * ((100 - $rpn) / $rpn);
    }

    public static function match(Systeme $systeme): bool
    {
        return $systeme->generateur()->type()->is_generateur_air_chaud()
            && $systeme->generateur()->energie()->is_combustible()
            && false === $systeme->generateur()->position()->generateur_multi_batiment;
    }
}
