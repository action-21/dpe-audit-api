<?php

namespace App\Api\Production\Model;

use App\Domain\Production\Entity\PanneauPhotovoltaique as Entity;
use App\Domain\Production\Entity\PanneauPhotovoltaiqueCollection as EntityCollection;
use App\Services\Validator\Constraints as DpeAssert;
use Symfony\Component\Validator\Constraints as Assert;

final class PanneauPhotovoltaique
{
    public function __construct(
        public string $id,

        public string $description,

        #[DpeAssert\Orientation]
        public float $orientation,

        #[DpeAssert\Inclinaison]
        public float $inclinaison,

        #[Assert\Positive]
        public int $modules,

        #[Assert\Positive]
        public ?float $surface,

        public ?PanneauPhotovoltaiqueData $data,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
            id: $entity->id(),
            description: $entity->description(),
            orientation: $entity->orientation()->value,
            inclinaison: $entity->inclinaison()->value,
            modules: $entity->modules(),
            surface: $entity->surface(),
            data: PanneauPhotovoltaiqueData::from($entity),
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
