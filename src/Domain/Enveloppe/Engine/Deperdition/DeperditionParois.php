<?php

namespace App\Domain\Enveloppe\Engine\Deperdition;

use App\Domain\Audit\Audit;
use App\Domain\Common\EngineRule;

final class DeperditionParois extends EngineRule
{
    public function apply(Audit $entity): void {}

    public static function dependencies(): array
    {
        return [
            DeperditionBaie::class,
            DeperditionMur::class,
            DeperditionPlancherBas::class,
            DeperditionPlancherHaut::class,
            DeperditionPorte::class,
        ];
    }
}
