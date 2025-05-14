<?php

namespace App\Engine\Performance\Chauffage\Rendement\RendementGeneration;

use App\Domain\Common\Enum\ZoneClimatique;

trait RendementPacHybride
{
    /**
     * Taux de couverture de la partie PAC
     */
    public static function taux_couverture_partie_chaudiere(ZoneClimatique $zone_climatique): float
    {
        return 1 - self::taux_couverture_partie_pac($zone_climatique);
    }

    /**
     * Taux de couverture de la partie PAC
     */
    public static function taux_couverture_partie_pac(ZoneClimatique $zone_climatique): float
    {
        return match ($zone_climatique) {
            ZoneClimatique::H1a, ZoneClimatique::H1b, ZoneClimatique::H1c => 0.8,
            ZoneClimatique::H2a, ZoneClimatique::H2b, ZoneClimatique::H2c, ZoneClimatique::H2d => 0.83,
            ZoneClimatique::H3 => 0.88,
        };
    }
}
