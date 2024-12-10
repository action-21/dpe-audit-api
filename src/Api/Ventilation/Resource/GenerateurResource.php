<?php

namespace App\Api\Ventilation\Resource;

use App\Domain\Common\Type\Id;
use App\Domain\Ventilation\Enum\{TypeGenerateur, TypeVmc};
use App\Domain\Ventilation\Entity\{Generateur as Entity, GenerateurCollection as EntityCollection};
use App\Domain\Ventilation\ValueObject\Signaletique;

final class GenerateurResource
{
    public function __construct(
        public readonly Id $id,
        public readonly string $description,
        public readonly Signaletique $signaletique,
        public readonly bool $generateur_collectif,
        public readonly ?int $annee_installation,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
            id: $entity->id(),
            description: $entity->description(),
            signaletique: $entity->signaletique(),
            generateur_collectif: $entity->generateur_collectif(),
            annee_installation: $entity->annee_installation(),
        );
    }

    /** @return self[] */
    public static function from_collection(EntityCollection $collection): array
    {
        return $collection->map(fn(Entity $item): self => self::from($item))->values();
    }
}
