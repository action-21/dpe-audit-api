<?php

namespace App\Application\Ecs\View;

use App\Domain\Common\Type\Id;
use App\Domain\Common\ValueObject\Consommation;
use App\Domain\Ecs\Entity\{Systeme as Entity, SystemeCollection as EntityCollection};
use App\Domain\Ecs\ValueObject\{Performance, Perte, Rendement, Reseau, Stockage};

/**
 * 
 * @property Perte[] $pertes_distribution
 * @property Perte[] $pertes_stockage
 * @property Rendement[] $rendements
 * @property Consommation[] $consommations
 * @property Consommation[] $consommations_auxiliaires
 */
final class Systeme
{
    public function __construct(
        public readonly Id $id,
        public readonly Id $generateur_id,
        public readonly Reseau $reseau,
        public readonly ?Stockage $stockage,
        public readonly ?float $rdim,
        public readonly ?Performance $performance,
        public readonly array $pertes_distribution,
        public readonly array $pertes_stockage,
        public readonly array $rendements,
        public readonly array $consommations,
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
