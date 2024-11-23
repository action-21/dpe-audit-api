<?php

namespace App\Domain\Mur\Service;

use App\Domain\Mur\Enum\Mitoyennete;
use App\Domain\Mur\Mur;

final class MoteurSurfaceDeperditive
{
    public function calcule_surface_deperditive(Mur $entity): float
    {
        if ($entity->mitoyennete() === Mitoyennete::LOCAL_RESIDENTIEL)
            return 0;

        $sdep = $entity->caracteristique()->surface;
        $sdep -= $entity->enveloppe()->parois()->baies()->filter_by_paroi(id: $entity->id())->surface_deperditive();
        $sdep -= $entity->enveloppe()->parois()->portes()->filter_by_paroi(id: $entity->id())->surface_deperditive();
        return \max($sdep, 0);
    }
}
