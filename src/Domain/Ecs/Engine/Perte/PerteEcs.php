<?php

namespace App\Domain\Ecs\Engine\Perte;

use App\Domain\Audit\Audit;
use App\Domain\Common\EngineRule;

final class PerteEcs extends EngineRule
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
