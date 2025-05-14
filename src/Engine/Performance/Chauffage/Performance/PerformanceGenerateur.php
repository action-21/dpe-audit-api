<?php

namespace App\Engine\Performance\Chauffage\Performance;

use App\Domain\Audit\Audit;
use App\Engine\Performance\Rule;

final class PerformanceGenerateur extends Rule
{
    public function apply(Audit $entity): void {}

    public static function dependencies(): array
    {
        return [
            PerformanceChaudiere::class,
            PerformanceGenerateurCombustion::class,
            PerformancePac::class,
        ];
    }
}
