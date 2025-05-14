<?php

namespace App\Engine\Performance\Ecs\Rendement;

use App\Domain\Common\Enum\ScenarioUsage;
use App\Domain\Ecs\Entity\Systeme;

final class RendementPac extends RendementSysteme
{
    public function rgs(ScenarioUsage $scenario): float
    {
        return $this->generateur()->data()->cop;
    }

    public static function match(Systeme $systeme): bool
    {
        return $systeme->generateur()->type()->is_pac()
            && false === $systeme->generateur()->position()->generateur_multi_batiment;
    }
}
