<?php

namespace App\Api\Ventilation\Resource;

use App\Domain\Common\Type\Id;
use App\Domain\Common\ValueObject\Consommation;
use App\Domain\Ventilation\Entity\{Systeme as Entity, SystemeCollection as EntityCollection};
use App\Domain\Ventilation\Enum\TypeVentilation;
use App\Domain\Ventilation\ValueObject\Performance;

/**
 * @property Consommation[] $consommations
 */
final class SystemeResource
{
    public function __construct(
        public readonly Id $id,
        public readonly ?Id $generateur_id,
        public readonly TypeVentilation $type_ventilation,
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
            type_ventilation: $entity->type_ventilation(),
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
