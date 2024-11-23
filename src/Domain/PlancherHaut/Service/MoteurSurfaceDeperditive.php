<?php

namespace App\Domain\PlancherHaut\Service;

use App\Domain\PlancherHaut\Enum\Mitoyennete;
use App\Domain\PlancherHaut\PlancherHaut;

final class MoteurSurfaceDeperditive
{
    public function calcule_surface_deperditive(PlancherHaut $entity): float
    {
        if ($entity->mitoyennete() === Mitoyennete::LOCAL_RESIDENTIEL)
            return 0;

        $sdep = $entity->caracteristique()->surface;
        $sdep -= $entity->enveloppe()->parois()->baies()->filter_by_paroi(id: $entity->id())->surface_deperditive();
        $sdep -= $entity->enveloppe()->parois()->portes()->filter_by_paroi(id: $entity->id())->surface_deperditive();
        return \max($sdep, 0);
    }
}
