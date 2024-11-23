<?php

namespace App\Application\Production\View;

use App\Domain\Common\Type\Id;
use App\Domain\Production\Entity\{PanneauPhotovoltaique as Entity, PanneauPhotovoltaiqueCollection as EntityCollection};
use App\Domain\Production\ValueObject\ProductionPhotovoltaique;

/**
 * @property ProductionPhotovoltaique[] $productions
 */
final class PanneauPhotovoltaique
{
    public function __construct(
        public readonly Id $id,
        public readonly float $orientation,
        public readonly float $inclinaison,
        public readonly int $modules,
        public readonly ?float $surface_capteurs,
        public readonly array $productions,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
            id: $entity->id(),
            orientation: $entity->orientation(),
            inclinaison: $entity->inclinaison(),
            modules: $entity->modules(),
            surface_capteurs: $entity->surface_capteurs(),
            productions: $entity->productions()?->values() ?? [],
        );
    }

    /** @return self[] */
    public static function from_collection(EntityCollection $collection): array
    {
        return $collection->map(fn(Entity $item): self => self::from($item))->values();
    }
}
