<?php

namespace App\Domain\Refroidissement\Service;

use App\Domain\Refroidissement\Entity\{Installation, Systeme};

final class MoteurDimensionnement
{
    public function calcule_dimensionnement(Systeme $entity): float
    {
        $rdim = $this->calcule_dimensionnement_systeme($entity);
        return $rdim *= $this->calcule_dimensionnement_installation($entity->installation());
    }

    public function calcule_dimensionnement_installation(Installation $entity): float
    {
        return $this->rdim_installation(
            surface_installation: $entity->surface(),
            surface_installations: $entity->refroidissement()->installations()->surface(),
        );
    }

    public function calcule_dimensionnement_systeme(Systeme $entity): float
    {
        return $this->rdim_systeme(systemes: $entity->installation()->systemes()->count());
    }

    /** 
     * Ratio de dimensionnement de l'installation
     * 
     * @param float $surface_installation - Surface de l'installation
     * @param float $surface_installations - Surface totale des installations
     */
    public function rdim_installation(float $surface_installation, float $surface_installations): float
    {
        return $surface_installations > 0 ? $surface_installation / $surface_installations : 0;
    }

    /** 
     * Ratio de dimensionnement du système
     * 
     * @param int $systemes - Nombre de systèmes composant l'installation
     */
    public function rdim_systeme(int $systemes): float
    {
        return $systemes > 0 ? 1 / $systemes : 0;
    }
}
