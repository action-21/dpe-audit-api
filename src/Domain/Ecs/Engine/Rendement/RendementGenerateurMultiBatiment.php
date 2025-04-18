<?php

namespace App\Domain\Ecs\Engine\Rendement;

use App\Domain\Common\Enum\ScenarioUsage;
use App\Domain\Ecs\Entity\Systeme;
use App\Domain\Ecs\Enum\IsolationReseau;

final class RendementGenerateurMultiBatiment extends RendementSysteme
{
    public function rgs(ScenarioUsage $scenario): float
    {
        return match ($this->systeme->reseau()->isolation) {
            IsolationReseau::ISOLE => 0.9,
            IsolationReseau::NON_ISOLE => 0.75,
            default => 0.75,
        };
    }

    public static function supports(Systeme $systeme): bool
    {
        return $systeme->generateur()->position()->generateur_multi_batiment;
    }
}
