<?php

namespace App\Api\Ecs\Model;

use App\Api\Common\Model\{Besoin, Perte};
use App\Domain\Common\ValueObject\{Consommation, Emission};
use App\Domain\Ecs\Ecs as Entity;

/**
 * @property array<Besoin> $besoins
 * @property array<Perte> $pertes
 * @property array<Perte> $pertes_recuperables
 * @property array<Consommation> $consommations
 * @property array<Emission> $emissions
 */
final class EcsData
{
    public function __construct(
        public readonly ?float $nmax,
        public readonly ?float $nadeq,
        /** @var Besoin[] */
        public readonly array $besoins,
        /** @var Perte[] */
        public readonly array $pertes,
        /** @var Perte[] */
        public readonly array $pertes_recuperables,
        /** @var Consommation[] */
        public readonly array $consommations,
        /** @var Emission[] */
        public readonly array $emissions,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
            nmax: $entity->data()->nmax,
            nadeq: $entity->data()->nadeq,
            besoins: $entity->data()->besoins ? Besoin::from($entity->data()->besoins) : [],
            pertes: $entity->data()->pertes ? Perte::from($entity->data()->pertes) : [],
            pertes_recuperables: $entity->data()->pertes_recuperables ? Perte::from($entity->data()->pertes_recuperables) : [],
            consommations: $entity->data()->consommations?->values() ?? [],
            emissions: $entity->data()->emissions?->values() ?? [],
        );
    }
}
