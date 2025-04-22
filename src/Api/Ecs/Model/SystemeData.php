<?php

namespace App\Api\Ecs\Model;

use App\Api\Common\Model\Perte;
use App\Domain\Common\ValueObject\{Consommation, Emission};
use App\Domain\Ecs\Entity\Systeme as Entity;
use App\Domain\Ecs\ValueObject\Rendement;

/**
 * @property array<Rendement> $rendements
 * @property array<Perte> $pertes
 * @property array<Perte> $pertes_recuperables
 * @property array<Consommation> $consommations
 * @property array<Emission> $emissions
 */
final class SystemeData
{
    public function __construct(
        public readonly ?float $rdim,
        /** @var Rendement[] */
        public readonly array $rendements,
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
            rdim: $entity->data()->rdim,
            rendements: $entity->data()->rendements?->values() ?? [],
            pertes: $entity->data()->pertes ? Perte::from($entity->data()->pertes) : [],
            pertes_recuperables: $entity->data()->pertes_recuperables ? Perte::from($entity->data()->pertes_recuperables) : [],
            consommations: $entity->data()->consommations?->values() ?? [],
            emissions: $entity->data()->emissions?->values() ?? [],
        );
    }
}
