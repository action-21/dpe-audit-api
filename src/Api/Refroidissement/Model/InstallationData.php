<?php

namespace App\Api\Refroidissement\Model;

use App\Domain\Common\ValueObject\{Consommation, Emission};
use App\Domain\Refroidissement\Entity\Installation as Entity;

/**
 * @property array<Consommation> $consommations
 * @property array<Emission> $emissions
 */
final class InstallationData
{
    public function __construct(
        public readonly ?float $rdim,
        /** @var array<Consommation> */
        public readonly array $consommations,
        /** @var array<Emission> */
        public readonly array $emissions,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
            rdim: $entity->data()->rdim,
            consommations: $entity->data()->consommations?->values() ?? [],
            emissions: $entity->data()->emissions?->values() ?? [],
        );
    }
}
