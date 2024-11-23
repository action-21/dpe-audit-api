<?php

namespace App\Domain\Audit\Service;

use App\Domain\Audit\Audit;

/**
 * TODO: Vérifier le verbatim dimensionnnement / virtualisation / proratisation
 */
final class MoteurDimensionnement
{
    /**
     * Dans le cas d'un audit au périmètre du bâtiment : ratio_virtualisation = 1
     */
    public function calcule_dimensionnement(Audit $entity): float
    {
        return $this->ratio_virtualisation(
            surface_logement: $entity->logement()?->surface_habitable ?? $entity->batiment()->surface_habitable,
            surface_batiment: $entity->batiment()->surface_habitable,
        );
    }

    public function ratio_virtualisation(float $surface_logement, float $surface_batiment): float
    {
        return $surface_batiment > 0 ? $surface_logement / $surface_batiment : 0;
    }
}
