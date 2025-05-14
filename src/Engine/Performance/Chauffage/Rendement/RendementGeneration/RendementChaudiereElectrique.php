<?php

namespace App\Engine\Performance\Chauffage\Rendement\RendementGeneration;

use App\Domain\Chauffage\Entity\Systeme;
use App\Domain\Chauffage\Enum\{EnergieGenerateur, TypeGenerateur};
use App\Domain\Common\Enum\{ScenarioUsage, ZoneClimatique};
use App\Engine\Performance\Chauffage\Rendement\RendementGeneration;

final class RendementChaudiereElectrique extends RendementGeneration
{
    use RendementPacHybride;

    public final const RG = 0.97;

    /**
     * @see \App\Engine\Performance\Scenario\ScenarioClimatique::zone_climatique()
     */
    public function zone_climatique(): ZoneClimatique
    {
        return $this->audit->data()->zone_climatique;
    }

    public function rg(ScenarioUsage $scenario): float
    {
        if ($this->generateur()->type()->is_pac_hybride()) {
            $rg_partie_chaudiere = static::RG * self::taux_couverture_partie_chaudiere(
                zone_climatique: $this->zone_climatique(),
            );
            $rg_partie_pac = $this->generateur()->data()->scop * self::taux_couverture_partie_pac(
                zone_climatique: $this->zone_climatique(),
            );
            return $rg_partie_chaudiere + $rg_partie_pac;
        }
        return static::RG;
    }

    public static function match(Systeme $systeme): bool
    {
        $type_generateur = $systeme->generateur()->type()->is_pac_hybride()
            ? TypeGenerateur::CHAUDIERE
            :  $systeme->generateur()->type();

        $energie_generateur =  $systeme->generateur()->type()->is_pac_hybride()
            ? $systeme->generateur()->energie_partie_chaudiere()
            : $systeme->generateur()->energie();

        return $type_generateur->is_chaudiere()
            && $energie_generateur === EnergieGenerateur::ELECTRICITE
            && false === $systeme->generateur()->position()->generateur_multi_batiment;
    }
}
