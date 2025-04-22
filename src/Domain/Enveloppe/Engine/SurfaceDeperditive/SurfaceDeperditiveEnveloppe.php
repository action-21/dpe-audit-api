<?php

namespace App\Domain\Enveloppe\Engine\SurfaceDeperditive;

use App\Domain\Audit\Audit;
use App\Domain\Common\EngineRule;
use App\Domain\Enveloppe\ValueObject\SurfacesDeperditives;

final class SurfaceDeperditiveEnveloppe extends EngineRule
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
