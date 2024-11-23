<?php

namespace App\Domain\PlancherBas\Service;

use App\Domain\PlancherBas\Enum\Mitoyennete;
use App\Domain\PlancherBas\PlancherBas;

final class MoteurSurfaceDeperditive
{
    public function calcule_surface_deperditive(PlancherBas $entity): float
    {
        if ($entity->mitoyennete() === Mitoyennete::LOCAL_RESIDENTIEL)
            return 0;

        $sdep = $entity->caracteristique()->surface;
        $sdep -= $entity->enveloppe()->parois()->baies()->filter_by_paroi(id: $entity->id())->surface_deperditive();
        $sdep -= $entity->enveloppe()->parois()->portes()->filter_by_paroi(id: $entity->id())->surface_deperditive();
        return \max($sdep, 0);
    }
}
