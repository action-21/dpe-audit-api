<?php

namespace App\Api\Refroidissement\Model;

use App\Api\Common\Model\Besoin;
use App\Domain\Common\ValueObject\{Consommation, Emission};
use App\Domain\Refroidissement\Refroidissement as Entity;

/**
 * @property array<Besoin> $besoins
 * @property array<Consommation> $consommations
 * @property array<Emission> $emissions
 */
final class RefroidissementData
{
    public function __construct(
        /**
         * @var array<Besoin>
         */
        public readonly array $besoins,
        /**
         * @var array<Consommation>
         */
        public readonly array $consommations,
        /**
         * @var array<Emission>
         */
        public readonly array $emissions,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
            besoins: $entity->data()->besoins ? Besoin::from($entity->data()->besoins) : [],
            consommations: $entity->data()->consommations?->values() ?? [],
            emissions: $entity->data()->emissions?->values() ?? [],
        );
    }
}
