<?php

namespace App\Api\Ecs\Model;

use App\Domain\Ecs\Entity\{Installation as Entity, InstallationCollection as EntityCollection};
use Symfony\Component\Validator\Constraints as Assert;

final class Installation
{
    public function __construct(
        #[Assert\Uuid]
        public readonly string $id,

        public readonly string $description,

        #[Assert\Positive]
        public readonly float $surface,

        #[Assert\Valid]
        public readonly ?Solaire $solaire_thermique,

        public readonly ?InstallationData $data,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
            id: $entity->id(),
            description: $entity->description(),
            surface: $entity->surface(),
            solaire_thermique: $entity->solaire_thermique() ? Solaire::from($entity) : null,
            data: InstallationData::from($entity),
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
