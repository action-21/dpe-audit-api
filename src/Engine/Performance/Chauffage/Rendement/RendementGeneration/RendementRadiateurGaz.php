<?php

namespace App\Engine\Performance\Chauffage\Rendement\RendementGeneration;

use App\Domain\Chauffage\Entity\Systeme;
use App\Domain\Chauffage\Enum\TauxCharge;
use App\Domain\Common\Enum\ScenarioUsage;

final class RendementRadiateurGaz extends RendementCombustion
{
    public function rg(ScenarioUsage $scenario): float
    {
        return $this->rg_combustion($scenario);
    }

    public function qp(ScenarioUsage $scenario, TauxCharge $x): float
    {
        $pn = $this->pn();
        $rpn = $this->rpn();
        $tch = $this->tch_final(scenario: $scenario, x: $x);
        return 1.04 * ((100 - $rpn) / $rpn) * $pn * $tch;
    }

    public static function match(Systeme $systeme): bool
    {
        return $systeme->generateur()->type()->is_radiateur_gaz();
    }
}
