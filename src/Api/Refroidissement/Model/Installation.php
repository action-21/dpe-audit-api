<?php

namespace App\Api\Refroidissement\Model;

use App\Domain\Refroidissement\Entity\{Installation as Entity, InstallationCollection as EntityCollection};
use Symfony\Component\Validator\Constraints as Assert;

final class Installation
{
    public function __construct(
        public string $id,

        public string $description,

        #[Assert\Positive]
        public float $surface,

        public ?InstallationData $data,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
            id: $entity->id(),
            description: $entity->description(),
            surface: $entity->surface(),
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
