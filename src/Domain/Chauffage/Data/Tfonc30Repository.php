<?php

namespace App\Domain\Chauffage\Data;

use App\Domain\Chauffage\Enum\CategorieGenerateur;
use App\Domain\Chauffage\Enum\TemperatureDistribution;

interface Tfonc30Repository
{
    public function find_by(
        CategorieGenerateur $categorie_generateur,
        TemperatureDistribution $temperature_distribution,
        int $annee_installation_generateur,
        int $annee_installation_emetteur,
    ): ?Tfonc30;
}
