<?php

namespace App\Engine\Performance\SurfaceDeperditive;

use App\Domain\Audit\Audit;
use App\Domain\Common\ValueObject\Annee;
use App\Domain\Enveloppe\Entity\PlancherHaut;
use App\Domain\Enveloppe\Enum\EtatIsolation;
use App\Domain\Enveloppe\ValueObject\SurfaceDeperditive;

/**
 * @extends SurfaceDeperditiveParoi<PlancherHaut>
 */
final class SurfaceDeperditivePlancherHaut extends SurfaceDeperditiveParoi
{
    private Audit $audit;

    public function annee_construction(): Annee
    {
        return current(array_filter([
            $this->paroi->annee_renovation(),
            $this->paroi->annee_construction(),
            $this->audit->batiment()->annee_construction,
        ]));
    }

    public function isolation(): EtatIsolation
    {
        if ($this->paroi->isolation()->etat_isolation) {
            return $this->paroi->isolation()->etat_isolation;
        }
        return $this->annee_construction()->less_than(1975)
            ? EtatIsolation::NON_ISOLE
            : EtatIsolation::ISOLE;
    }

    public function apply(Audit $entity): void
    {
        $this->audit = $entity;

        foreach ($entity->enveloppe()->planchers_hauts() as $paroi) {
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
