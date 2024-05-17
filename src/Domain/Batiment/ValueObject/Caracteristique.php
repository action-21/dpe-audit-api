<?php

namespace App\Domain\Batiment\ValueObject;

use App\Domain\Batiment\Enum\{ClasseAltitude, PeriodeConstruction};

final class Caracteristique
{
    public readonly ClasseAltitude $classe_altitude;
    public readonly PeriodeConstruction $periode_construction;

    public function __construct(
        public readonly Altitude $altitude,
        public readonly AnneeConstruction $annee_construction,
        public readonly Logements $nombre_logements,
    ) {
        $this->classe_altitude = ClasseAltitude::from_altitude($altitude->valeur());
        $this->periode_construction = PeriodeConstruction::from_annee_construction($annee_construction->valeur());
    }
}
