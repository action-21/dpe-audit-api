<?php

namespace App\Api\Enveloppe\Model;

use App\Api\Enveloppe\Model\PontThermique\{Data, Liaison};
use App\Domain\Enveloppe\Entity\{PontThermique as Entity, PontThermiqueCollection as EntityCollection};
use Symfony\Component\Validator\Constraints as Assert;

final class PontThermique
{
    public function __construct(
        public string $id,

        public string $description,

        #[Assert\Positive]
        public float $longueur,

        #[Assert\Positive]
        public ?float $kpt,

        #[Assert\Valid]
        public Liaison $liaison,

        public ?Data $data,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
            id: $entity->id(),
            description: $entity->description(),
            longueur: $entity->longueur(),
            kpt: $entity->kpt(),
            liaison: Liaison::from($entity),
            data: Data::from($entity),
        );
    }

    public static function from_collection(EntityCollection $collection): array
    {
        return $collection->map(fn(Entity $entity) => self::from($entity))->values();
    }
}
