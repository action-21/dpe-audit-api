<?php

namespace App\Engine\Performance\Chauffage\Rendement\RendementGeneration;

use App\Domain\Chauffage\Entity\Systeme;
use App\Domain\Chauffage\Enum\TypeGenerateur;
use App\Domain\Common\Enum\ScenarioUsage;
use App\Engine\Performance\Chauffage\Rendement\RendementGeneration;

final class RendementReseauChaleur extends RendementGeneration
{
    public final const RG = 0.97;

    public function rg(ScenarioUsage $scenario): float
    {
        return static::RG;
    }

    public static function match(Systeme $systeme): bool
    {
        return $systeme->generateur()->type() === TypeGenerateur::RESEAU_CHALEUR
            || $systeme->generateur()->position()->generateur_multi_batiment;
    }
}
