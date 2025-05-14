<?php

namespace App\Api\Enveloppe\Model;

use App\Api\Enveloppe\Model\Lnc\{Baie, Data, ParoiOpaque};
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
        public string $id,

        public string $description,

        public TypeLnc $type,

        /** @var ParoiOpaque[] */
        #[Assert\All([new Assert\Type(ParoiOpaque::class)])]
        #[Assert\Valid]
        public array $parois_opaques,

        /** @var Baie[] */
        #[Assert\All([new Assert\Type(Baie::class)])]
        #[Assert\Valid]
        public array $baies,

        public ?Data $data,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
            id: $entity->id(),
            description: $entity->description(),
            type: $entity->type(),
            parois_opaques: ParoiOpaque::from_collection($entity->parois_opaques()),
            baies: Baie::from_collection($entity->baies()),
            data: Data::from($entity),
        );
    }

    public static function from_collection(EntityCollection $collection): array
    {
        return $collection->map(fn(Entity $entity) => self::from($entity))->to_array();
    }
}
