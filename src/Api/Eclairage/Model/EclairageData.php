<?php

namespace App\Api\Eclairage\Model;

use App\Domain\Common\ValueObject\{Consommation, Emission};
use App\Domain\Eclairage\Eclairage as Entity;

/**
 * @property array<Consommation> $consommations
 * @property array<Emission> $emissions
 */
final class EclairageData
{
    public function __construct(
        /** @var array<Consommation> */
        public readonly array $consommations,
        /** @var array<Emission> */
        public readonly array $emissions,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
            consommations: $entity->data()->consommations?->values() ?? [],
            emissions: $entity->data()->emissions?->values() ?? [],
        );
    }
}
