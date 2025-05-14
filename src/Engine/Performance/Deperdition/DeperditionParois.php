<?php

namespace App\Engine\Performance\Deperdition;

use App\Domain\Audit\Audit;
use App\Engine\Performance\Rule;

final class DeperditionParois extends Rule
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
