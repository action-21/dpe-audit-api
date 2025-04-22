<?php

namespace App\Api\Chauffage\Model;

use App\Domain\Common\ValueObject\{Consommation, Emission};
use App\Domain\Chauffage\Entity\Installation as Entity;

/**
 * @property array<Consommation> $consommations
 * @property array<Emission> $emissions
 */
final class InstallationData
{
    public function __construct(
        public readonly ?float $rdim,
        public readonly ?float $fch,
        /** @var Consommation[] */
        public readonly array $consommations,
        /** @var Emission[] */
        public readonly array $emissions,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
            rdim: $entity->data()->rdim,
            fch: $entity->data()->fch?->value(),
            consommations: $entity->data()->consommations?->values() ?? [],
            emissions: $entity->data()->emissions?->values() ?? [],
        );
    }
}
