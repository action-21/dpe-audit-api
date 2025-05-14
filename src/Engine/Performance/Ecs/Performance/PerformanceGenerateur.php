<?php

namespace App\Engine\Performance\Ecs\Performance;

use App\Domain\Audit\Audit;
use App\Engine\Performance\Rule;

final class PerformanceGenerateur extends Rule
{
    public function apply(Audit $entity): void {}

    public static function dependencies(): array
    {
        return [
            PerformanceGenerateurCombustion::class,
            PerformancePac::class,
        ];
    }
}
