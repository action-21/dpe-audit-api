<?php

namespace App\Domain\Enveloppe\Engine\SurfaceDeperditive;

use App\Domain\Audit\Audit;
use App\Domain\Common\EngineRule;

final class SurfaceDeperditiveEnveloppe extends EngineRule
{
    public function apply(Audit $entity): void {}

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
