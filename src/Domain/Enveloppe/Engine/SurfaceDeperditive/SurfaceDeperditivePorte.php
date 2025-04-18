<?php

namespace App\Domain\Enveloppe\Engine\SurfaceDeperditive;

use App\Domain\Audit\Audit;
use App\Domain\Enveloppe\Entity\Porte;
use App\Domain\Enveloppe\Enum\EtatIsolation;
use App\Domain\Enveloppe\ValueObject\SurfaceDeperditive;

/**
 * @property Porte $paroi
 */
final class SurfaceDeperditivePorte extends SurfaceDeperditiveParoi
{
    public function isolation(): EtatIsolation
    {
        return $this->paroi->isolation() ?? EtatIsolation::NON_ISOLE;
    }

    public function apply(Audit $entity): void
    {
        foreach ($entity->enveloppe()->portes() as $paroi) {
            $this->paroi = $paroi;

            $paroi->calcule($paroi->data()->with(
                sdep: $this->sdep(),
                isolation: $this->isolation(),
            ));

            $entity->enveloppe()->calcule($entity->enveloppe()->data()->add_surface_deperditive(SurfaceDeperditive::create(
                type: $paroi->type_paroi(),
                isolation: $this->isolation(),
                sdep: $this->sdep(),
            )));
        }
    }
}
