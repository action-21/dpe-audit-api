<?php

namespace App\Api\Baie\Resource;

use App\Domain\Baie\{Baie as Entity, BaieCollection as EntityCollection};
use App\Domain\Baie\ValueObject\{Caracteristique, DoubleFenetre, Ensoleillement, Performance, Position};
use App\Domain\Common\Type\Id;

final class BaieResource
{
    public function __construct(
        public readonly Id $id,
        public readonly string $description,
        public readonly Position $position,
        public readonly Caracteristique $caracteristique,
        public readonly ?DoubleFenetre $double_fenetre,
        public readonly ?Performance $performance,
        /** @var MasqueProcheResource[] */
        public readonly array $masques_proches,
        /** @var MasqueLointainResource[] */
        public readonly array $masques_lointains,
        /** @var Ensoleillement[] */
        public readonly array $ensoleillements,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
            id: $entity->id(),
            description: $entity->description(),
            position: $entity->position(),
            caracteristique: $entity->caracteristique(),
            double_fenetre: $entity->double_fenetre(),
            performance: $entity->performance(),
            masques_proches: MasqueProcheResource::from_collection($entity->masques_proches()),
            masques_lointains: MasqueLointainResource::from_collection($entity->masques_lointains()),
            ensoleillements: $entity->ensoleillement()?->values() ?? [],
        );
    }

    /** @return self[] */
    public static function from_collection(EntityCollection $collection): array
    {
        return $collection->map(fn(Entity $entity) => self::from($entity))->values();
    }
}
