<?php

namespace App\Api\Ecs\Resource;

use App\Domain\Common\Type\Id;
use App\Domain\Common\ValueObject\Consommation;
use App\Domain\Ecs\Entity\{Systeme as Entity, SystemeCollection as EntityCollection};
use App\Domain\Ecs\ValueObject\{Performance, Perte, Rendement, Reseau, Stockage};

final class SystemeResource
{
    public function __construct(
        public readonly Id $id,
        public readonly Id $generateur_id,
        public readonly Reseau $reseau,
        public readonly ?Stockage $stockage,
        public readonly ?float $rdim,
        public readonly ?Performance $performance,
        /** @var Perte[] */
        public readonly array $pertes_distribution,
        /** @var Perte[] */
        public readonly array $pertes_stockage,
        /** @var Rendement[] */
        public readonly array $rendements,
        /** @var Consommation[] */
        public readonly array $consommations,
        /** @var Consommation[] */
        public readonly array $consommations_auxiliaires,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
            id: $entity->id(),
            generateur_id: $entity->generateur()->id(),
            reseau: $entity->reseau(),
            stockage: $entity->stockage(),
            rdim: $entity->rdim(),
            performance: $entity->generateur()->performance(),
            pertes_distribution: $entity->pertes_distribution()?->values() ?? [],
            pertes_stockage: $entity->pertes_stockage()?->values() ?? [],
            rendements: $entity->rendements()?->values() ?? [],
            consommations: $entity->consommations()?->values() ?? [],
            consommations_auxiliaires: $entity->consommations_auxiliaires()?->values() ?? [],
        );
    }

    /** @return self[] */
    public static function from_collection(EntityCollection $collection): array
    {
        return $collection->map(fn(Entity $entity) => self::from($entity))->values();
    }
}
