<?php

namespace App\Api\Resource\Ventilation;

use App\Domain\Common\Type\Id;
use App\Domain\Common\ValueObject\Consommation;
use App\Domain\Ventilation\Entity\{Systeme as Entity, SystemeCollection as EntityCollection};
use App\Domain\Ventilation\Enum\{ModeExtraction, ModeInsufflation, TypeSysteme};
use App\Domain\Ventilation\ValueObject\Performance;
use ApiPlatform\Metadata\{ApiProperty, ApiResource};

final class Systeme
{
    public function __construct(
        #[ApiProperty(identifier: true, readable: false, writable: false)]
        public readonly Id $id,
        public readonly ?Id $generateur_id,
        public readonly TypeSysteme $type,
        public readonly ?ModeExtraction $mode_extraction,
        public readonly ?ModeInsufflation $mode_insufflation,
        public readonly ?float $rdim,
        public readonly ?Performance $performance,
        /** @var Consommation[] */
        public readonly array $consommations,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
            id: $entity->id(),
            generateur_id: $entity->generateur()?->id(),
            type: $entity->type(),
            mode_extraction: $entity->mode_extraction(),
            mode_insufflation: $entity->mode_insufflation(),
            rdim: $entity->rdim(),
            performance: $entity->performance(),
            consommations: $entity->consommations()?->values() ?? [],
        );
    }

    /** @return self[] */
    public static function from_collection(EntityCollection $collection): array
    {
        return $collection->map(fn(Entity $item): self => self::from($item))->values();
    }
}
