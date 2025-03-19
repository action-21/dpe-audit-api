<?php

namespace App\Api\Chauffage\Resource;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Chauffage\Entity\{Emetteur as Entity, EmetteurCollection as EntityCollection};
use App\Domain\Chauffage\Enum\{TemperatureDistribution, TypeEmetteur, TypeEmission};

final class EmetteurResource
{
    public function __construct(
        public readonly Id $id,
        public readonly string $description,
        public readonly TypeEmetteur $type,
        public readonly TypeEmission $type_emission,
        public readonly TemperatureDistribution $temperature_distribution,
        public readonly bool $presence_robinet_thermostatique,
        public readonly ?int $annee_installation,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
            id: $entity->id(),
            description: $entity->description(),
            type: $entity->type(),
            type_emission: $entity->type_emission(),
            temperature_distribution: $entity->temperature_distribution(),
            presence_robinet_thermostatique: $entity->presence_robinet_thermostatique(),
            annee_installation: $entity->annee_installation(),
        );
    }

    /** @return self[] */
    public static function from_collection(EntityCollection $collection): array
    {
        return $collection->map(fn(Entity $entity) => self::from($entity))->values();
    }
}
