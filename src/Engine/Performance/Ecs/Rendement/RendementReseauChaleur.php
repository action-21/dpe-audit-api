<?php

namespace App\Engine\Performance\Ecs\Rendement;

use App\Domain\Common\Enum\ScenarioUsage;
use App\Domain\Ecs\Entity\Systeme;
use App\Domain\Ecs\Enum\{IsolationReseau, TypeGenerateur};

final class RendementReseauChaleur extends RendementSysteme
{
    public function rgs(ScenarioUsage $scenario): float
    {
        return match ($this->systeme->reseau()->isolation) {
            IsolationReseau::ISOLE => 0.9,
            IsolationReseau::NON_ISOLE => 0.75,
            default => 0.75,
        };
    }

    public static function match(Systeme $systeme): bool
    {
        return $systeme->generateur()->type() === TypeGenerateur::RESEAU_CHALEUR;
    }
}
