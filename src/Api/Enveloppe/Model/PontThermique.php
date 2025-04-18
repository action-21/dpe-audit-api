<?php

namespace App\Api\Enveloppe\Model;

use App\Api\Enveloppe\Model\PontThermique\Liaison;
use App\Domain\Enveloppe\Entity\{PontThermique as Entity, PontThermiqueCollection as EntityCollection};
use Symfony\Component\Validator\Constraints as Assert;

final class PontThermique
{
    public function __construct(
        #[Assert\Uuid]
        public readonly string $id,

        public readonly string $description,

        #[Assert\Positive]
        public readonly float $longueur,

        #[Assert\Positive]
        public readonly ?float $kpt,

        #[Assert\Valid]
        public readonly Liaison $liaison,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
            id: $entity->id(),
            description: $entity->description(),
            longueur: $entity->longueur(),
            kpt: $entity->kpt(),
            liaison: Liaison::from($entity),
        );
    }

    public static function from_collection(EntityCollection $collection): array
    {
        return $collection->map(fn(Entity $entity) => self::from($entity))->values();
    }
}
