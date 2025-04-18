<?php

namespace App\Api\Enveloppe\Model;

use App\Api\Enveloppe\Model\Lnc\{Baie, ParoiOpaque};
use App\Domain\Enveloppe\Entity\{Lnc as Entity, LncCollection as EntityCollection};
use App\Domain\Enveloppe\Enum\Lnc\TypeLnc;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @property ParoiOpaque[] $parois_opaques
 * @property Baie[] $baies
 */
final class Lnc
{
    public function __construct(
        #[Assert\Uuid]
        public readonly string $id,

        public readonly string $description,

        public readonly TypeLnc $type,

        #[Assert\All([new Assert\Type(ParoiOpaque::class)])]
        #[Assert\Valid]
        public readonly array $parois_opaques,

        #[Assert\All([new Assert\Type(Baie::class)])]
        #[Assert\Valid]
        public readonly array $baies,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
            id: $entity->id(),
            description: $entity->description(),
            type: $entity->type(),
            parois_opaques: ParoiOpaque::from_collection($entity->parois_opaques()),
            baies: Baie::from_collection($entity->baies()),
        );
    }

    public static function from_collection(EntityCollection $collection): array
    {
        return $collection->map(fn(Entity $entity) => self::from($entity))->to_array();
    }
}
