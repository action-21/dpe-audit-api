<?php

namespace App\Api\Resource\Lnc;

use App\Domain\Common\Type\Id;
use App\Domain\Lnc\{Lnc as Entity, LncCollection as EntityCollection};
use App\Domain\Lnc\Enum\TypeLnc;
use App\Domain\Lnc\ValueObject\{Ensoleillement, Performance};
use ApiPlatform\Metadata\{ApiProperty, ApiResource};

final class Lnc
{
    public function __construct(
        #[ApiProperty(identifier: true, readable: false, writable: false)]
        public readonly Id $id,
        public readonly string $description,
        public readonly TypeLnc $type,
        public readonly ?float $aiu,
        public readonly ?float $aue,
        public readonly ?bool $isolation_aiu,
        public readonly ?bool $isolation_aue,
        public readonly ?Performance $performance,
        /** @var Paroi[] */
        public readonly array $parois,
        /** @var Baie[] */
        public readonly array $baies,
        /** @var Ensoleillement[] */
        public readonly array $ensoleillements,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
            id: $entity->id(),
            description: $entity->description(),
            type: $entity->type(),
            aiu: $entity->aiu(),
            aue: $entity->aue(),
            isolation_aiu: $entity->isolation_aiu(),
            isolation_aue: $entity->isolation_aue(),
            performance: $entity->performance(),
            parois: Paroi::from_collection($entity->parois()),
            baies: Baie::from_collection($entity->baies()),
            ensoleillements: $entity->ensoleillement()?->values() ?? [],
        );
    }

    /** @return self[] */
    public static function from_collection(EntityCollection $collection): array
    {
        return $collection->map(fn(Entity $item): self => self::from($item))->values();
    }
}
