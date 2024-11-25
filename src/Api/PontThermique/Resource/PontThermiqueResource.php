<?php

namespace App\Api\PontThermique\Resource;

use App\Domain\Common\Type\Id;
use App\Domain\PontThermique\{PontThermique as Entity, PontThermiqueCollection as EntityCollection};
use App\Domain\PontThermique\ValueObject\{Liaison, Performance};

final class PontThermiqueResource
{
    public function __construct(
        public readonly Id $id,
        public readonly string $description,
        public readonly float $longueur,
        public readonly Liaison $liaison,
        public readonly ?float $kpt,
        public readonly ?Performance $performance,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
            id: $entity->id(),
            description: $entity->description(),
            longueur: $entity->longueur(),
            liaison: $entity->liaison(),
            kpt: $entity->kpt(),
            performance: $entity->performance(),
        );
    }

    /** @return self[] */
    public static function from_collection(EntityCollection $collection): array
    {
        return $collection->map(fn(Entity $entity) => self::from($entity))->values();
    }
}
