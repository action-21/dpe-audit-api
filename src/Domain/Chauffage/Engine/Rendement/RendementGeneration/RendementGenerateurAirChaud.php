<?php

namespace App\Domain\Chauffage\Engine\Rendement\RendementGeneration;

use App\Domain\Chauffage\Entity\Systeme;
use App\Domain\Common\Enum\ScenarioUsage;

final class RendementGenerateurAirChaud extends RendementCombustion
{
    public function rg(ScenarioUsage $scenario): float
    {
        return $this->rg_combustion($scenario);
    }

    public function qp(float $x): float
    {
        $qp0 = $this->qp0();
        $pn = $this->pn();
        $rpn = $this->rpn();
        $rpint = $this->rpint();

        if ($x == 50) {
            return 0.5 * $pn * ((100 - $rpint) / $rpint);
        }
        if ($x == 100) {
            return $pn * ((100 - $rpn) / $rpn);
        }
        if ($x < 50) {
            $qp50 = $this->qp(50);
            return ((($qp50 - 0.15 * $qp0) * $x) / 0.5) + 0.15 * $qp0;
        }
        if ($x < 100) {
            $qp50 = $this->qp(50);
            $qp100 = $this->qp(100);
            return ((($qp100 - $qp50) * $x) / 0.5) + 2 * $qp50 - $qp100;
        }
        return 0;
    }

    public static function supports(Systeme $systeme): bool
    {
        return $systeme->generateur()->type()->is_generateur_air_chaud()
            && $systeme->generateur()->energie()->is_combustible()
            && false === $systeme->generateur()->position()->generateur_multi_batiment;
    }
}
