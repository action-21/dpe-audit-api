<?php

namespace App\Api\Ventilation\Model;

use App\Domain\Ventilation\Entity\{Systeme as Entity, SystemeCollection as EntityCollection};
use App\Domain\Ventilation\Enum\TypeVentilation;
use Symfony\Component\Validator\Constraints as Assert;

final class Systeme
{
    public function __construct(
        public string $id,

        public string $installation_id,

        public ?string $generateur_id,

        public TypeVentilation $type,

        public ?SystemeData $data,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
            id: $entity->id(),
            installation_id: $entity->installation()->id(),
            generateur_id: $entity->generateur()?->id(),
            type: $entity->type(),
            data: SystemeData::from($entity),
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
