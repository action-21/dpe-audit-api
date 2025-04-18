<?php

namespace App\Domain\Chauffage\Engine\Rendement\RendementGeneration;

use App\Domain\Chauffage\Entity\Systeme;
use App\Domain\Common\Enum\ScenarioUsage;

final class RendementRadiateurGaz extends RendementCombustion
{
    public function rg(ScenarioUsage $scenario): float
    {
        return $this->rg_combustion($scenario);
    }

    public function qp(float $x): float
    {
        return 1.04 * ((100 - $this->rpn()) / $this->rpn()) * $this->pn() * $x;
    }

    public static function supports(Systeme $systeme): bool
    {
        return $systeme->generateur()->type()->is_radiateur_gaz();
    }
}
