<?php

namespace App\Engine\Performance\SurfaceDeperditive;

use App\Domain\Audit\Audit;
use App\Domain\Enveloppe\ValueObject\SurfacesDeperditives;
use App\Engine\Performance\Rule;

final class SurfaceDeperditiveEnveloppe extends Rule
{
    public function apply(Audit $entity): void
    {
        if (null === $entity->enveloppe()->data()->surfaces_deperditives) {
            $entity->enveloppe()->calcule($entity->enveloppe()->data()->with(
                surfaces_deperditives: SurfacesDeperditives::create()
            ));
        }
    }

    public static function dependencies(): array
    {
        return [
            SurfaceDeperditiveBaie::class,
            SurfaceDeperditiveMur::class,
            SurfaceDeperditivePlancherBas::class,
            SurfaceDeperditivePlancherHaut::class,
            SurfaceDeperditivePorte::class,
        ];
    }
}
