<?php

namespace App\Domain\Audit\Engine;

use App\Domain\Audit\Audit;
use App\Domain\Common\EngineRule;

final class ZoneThermique extends EngineRule
{
    private Audit $audit;

    /**
     * Bâtiment chauffé majoritairement par effet joule
     */
    public function effet_joule(): float
    {
        return $this->audit->chauffage()->installations()->with_effet_joule()->surface()
            > $this->audit->chauffage()->installations()->surface() / 2;
    }

    /**
     * Surface habitable de référence exprimée en m²
     */
    public function surface_habitable(): float
    {
        return $this->audit->batiment()->surface_habitable;
    }

    /**
     * Hauteur sous plafond de référence exprimée en mètre
     */
    public function hauteur_sous_plafond(): float
    {
        return $this->audit->batiment()->hauteur_sous_plafond;
    }

    /**
     * Volume habitable de référence exprimé en m³
     */
    public function volume_habitable(): float
    {
        return $this->surface_habitable() * $this->hauteur_sous_plafond();
    }

    /**
     * Nombre de logements de référence
     */
    public function nombre_logements(): int
    {
        return $this->audit->batiment()->logements;
    }

    public function apply(Audit $entity): void
    {
        $this->audit = $entity;
        $entity->calcule($entity->data()->with(
            effet_joule: $this->effet_joule(),
            surface_habitable: $this->surface_habitable(),
            hauteur_sous_plafond: $this->hauteur_sous_plafond(),
            volume_habitable: $this->volume_habitable(),
            nombre_logements: $this->nombre_logements(),
        ));
    }
}
