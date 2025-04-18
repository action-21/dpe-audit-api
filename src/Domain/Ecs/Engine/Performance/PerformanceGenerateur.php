<?php

namespace App\Domain\Ecs\Engine\Performance;

use App\Domain\Audit\Audit;
use App\Domain\Common\EngineRule;

final class PerformanceGenerateur extends EngineRule
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
