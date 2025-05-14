<?php

namespace App\Engine\Performance\Chauffage\Rendement\RendementGeneration;

use App\Domain\Chauffage\Entity\Systeme;
use App\Domain\Chauffage\Enum\EnergieGenerateur;
use App\Domain\Common\Enum\ScenarioUsage;
use App\Engine\Performance\Chauffage\Rendement\RendementGeneration;

final class RendementGenerateurEffetJoule extends RendementGeneration
{
    public final const RG = 1;

    public function rg(ScenarioUsage $scenario): float
    {
        return static::RG;
    }

    public static function match(Systeme $systeme): bool
    {
        return $systeme->generateur()->energie() === EnergieGenerateur::ELECTRICITE
            && false === $systeme->generateur()->type()->is_chaudiere()
            && false === $systeme->generateur()->position()->generateur_multi_batiment;
    }
}
