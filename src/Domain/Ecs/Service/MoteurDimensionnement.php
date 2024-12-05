<?php

namespace App\Domain\Ecs\Service;

use App\Domain\Ecs\Entity\{Generateur, Installation, Systeme};

final class MoteurDimensionnement
{
    public function calcule_dimensionnement(Systeme $entity): float
    {
        $rdim = $this->calcule_dimensionnement_installation($entity->installation());
        return $rdim *= $this->calcule_dimensionnement_systeme($entity);
    }

    public function calcule_dimensionnement_installation(Installation $entity): float
    {
        return $this->rdim_installation(
            surface_installation: $entity->surface(),
            surface_installations: $entity->ecs()->installations()->surface(),
        );
    }

    public function calcule_dimensionnement_systeme(Systeme $entity): float
    {
        return $this->rdim_systeme(systemes: $entity->installation()->systemes()->count());
    }

    public function calcule_pecs(Generateur $entity): float
    {
        $pecs = $this->pecs(volume_stockage: $entity->signaletique()->volume_stockage);

        /** @var Installation */
        foreach ($entity->ecs()->installations() as $installation) {
            /** @var Systeme */
            foreach ($installation->systemes() as $systeme) {
                if (false === $entity->id()->compare($systeme->generateur()->id()))
                    continue;

                $pecs = max($pecs, $this->pecs(volume_stockage: $systeme->stockage()?->volume_stockage ?? 0));
            }
        }
        return $pecs;
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

    /**
     * Puissance de dimensionnement du besoin d'eau chaude sanitaire en kW
     * 
     * @param float $volume_stockage - Volume de stockage en litres
     */
    public function pecs(float $volume_stockage): float
    {
        return match (true) {
            $volume_stockage === 0 => 21,
            $volume_stockage <= 20 => 21 - 0.8 * $volume_stockage,
            $volume_stockage <= 150 => 5 - 1.751 * (($volume_stockage - 20) / 65),
            $volume_stockage > 150 => (7.14 * $volume_stockage + 428) / 1000,
        };
    }
}
