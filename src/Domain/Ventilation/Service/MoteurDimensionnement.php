<?php

namespace App\Domain\Ventilation\Service;

use App\Domain\Ventilation\Entity\{Installation, Systeme};

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
            surface_installations: $entity->ventilation()->installations()->surface(),
        );
    }

    public function calcule_dimensionnement_systeme(Systeme $entity): float
    {
        return $this->rdim_systeme(systemes: $entity->installation()->systemes()->count());
    }

    public function rdim_installation(float $surface_installation, float $surface_installations): float
    {
        return $surface_installations > 0 ? $surface_installation / $surface_installations : 0;
    }

    public function rdim_systeme(int $systemes): float
    {
        return $systemes > 0 ? 1 / $systemes : 0;
    }
}
