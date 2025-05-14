<?php

namespace App\Api\Enveloppe\Model\Lnc;

use App\Domain\Enveloppe\Entity\Lnc\{ParoiOpaque as Entity, ParoiOpaqueCollection as EntityCollection};
use App\Domain\Enveloppe\Enum\EtatIsolation;
use Symfony\Component\Validator\Constraints as Assert;

final class ParoiOpaque
{
    public function __construct(
        public string $id,

        public string $description,

        public ?EtatIsolation $isolation,

        #[Assert\Valid]
        public PositionParoiOpaque $position,

        public ?ParoiOpaqueData $data,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
            id: $entity->id(),
            description: $entity->description(),
            isolation: $entity->isolation(),
            position: PositionParoiOpaque::from($entity),
            data: ParoiOpaqueData::from($entity),
        );
    }

    public static function from_collection(EntityCollection $collection): array
    {
        return $collection->map(fn(Entity $entity) => self::from($entity))->to_array();
    }
}
