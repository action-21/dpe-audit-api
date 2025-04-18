<?php

namespace App\Api\Chauffage\Model;

use App\Domain\Chauffage\Entity\{Emetteur as Entity, EmetteurCollection as EntityCollection};
use App\Domain\Chauffage\Enum\{TemperatureDistribution, TypeEmetteur};
use App\Services\Validator\Constraints as DpeAssert;
use Symfony\Component\Validator\Constraints as Assert;

final class Emetteur
{
    public function __construct(
        #[Assert\Uuid]
        public readonly string $id,

        public readonly string $description,

        public readonly TypeEmetteur $type,

        public readonly TemperatureDistribution $temperature_distribution,

        public readonly bool $presence_robinet_thermostatique,

        #[DpeAssert\Annee]
        public readonly ?int $annee_installation,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
            id: $entity->id(),
            description: $entity->description(),
            type: $entity->type(),
            temperature_distribution: $entity->temperature_distribution(),
            presence_robinet_thermostatique: $entity->presence_robinet_thermostatique(),
            annee_installation: $entity->annee_installation()?->value,
        );
    }

    /**
     * @return self[]
     */
    public static function from_collection(EntityCollection $collection): array
    {
        return $collection->map(fn(Entity $entity) => self::from($entity))->to_array();
    }
}
