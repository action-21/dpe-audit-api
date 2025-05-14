<?php

namespace App\Engine\Performance\Ecs\Perte;

use App\Domain\Audit\Audit;
use App\Engine\Performance\Rule;

final class PerteEcs extends Rule
{
    public function apply(Audit $entity): void {}

    public static function dependencies(): array
    {
        return [
            PerteGeneration::class,
            PerteDistribution::class,
            PerteStockageIndependant::class,
            PerteStockageIntegre::class,
        ];
    }
}
