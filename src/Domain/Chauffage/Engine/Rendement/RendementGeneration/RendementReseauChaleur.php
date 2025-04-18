<?php

namespace App\Domain\Chauffage\Engine\Rendement\RendementGeneration;

use App\Domain\Chauffage\Engine\Rendement\RendementGeneration;
use App\Domain\Chauffage\Entity\Systeme;
use App\Domain\Chauffage\Enum\TypeGenerateur;
use App\Domain\Common\Enum\ScenarioUsage;

final class RendementReseauChaleur extends RendementGeneration
{
    public final const RG = 0.97;

    public function rg(ScenarioUsage $scenario): float
    {
        return static::RG;
    }

    public static function supports(Systeme $systeme): bool
    {
        return $systeme->generateur()->type() === TypeGenerateur::RESEAU_CHALEUR
            || $systeme->generateur()->position()->generateur_multi_batiment;
    }
}
