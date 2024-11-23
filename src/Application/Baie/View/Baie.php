<?php

namespace App\Application\Baie\View;

use App\Domain\Baie\{Baie as Entity, BaieCollection as EntityCollection};
use App\Domain\Baie\ValueObject\{Caracteristique, DoubleFenetre, Ensoleillement, Performance, Position};
use App\Domain\Common\Type\Id;

/**
 * @property MasqueLointain[] $masques_lointains
 * @property MasqueProche[] $masques_proches
 * @property Ensoleillement[] $ensoleillements
 */
final class Baie
{
    public function __construct(
        public readonly Id $id,
        public readonly string $description,
        public readonly Position $position,
        public readonly Caracteristique $caracteristique,
        public readonly ?DoubleFenetre $double_fenetre,
        public readonly ?Performance $performance,
        public readonly array $masques_proches,
        public readonly array $masques_lointains,
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
            masques_proches: MasqueProche::from_collection($entity->masques_proches()),
            masques_lointains: MasqueLointain::from_collection($entity->masques_lointains()),
            ensoleillements: $entity->ensoleillement()?->values() ?? [],
        );
    }

    /** @return self[] */
    public static function from_collection(EntityCollection $collection): array
    {
        return $collection->map(fn(Entity $entity) => self::from($entity))->values();
    }
}
