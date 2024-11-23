<?php

namespace App\Application\Chauffage\View;

use App\Domain\Common\Type\Id;
use App\Domain\Common\ValueObject\Consommation;
use App\Domain\Chauffage\Entity\{Systeme as Entity, SystemeCollection as EntityCollection};
use App\Domain\Chauffage\Enum\TypeDistribution;
use App\Domain\Chauffage\ValueObject\{Performance, Rendement, Reseau};

/**
 * @property Emetteur[] $emetteurs
 * @property Rendement[] $rendements
 * @property Consommation[] $consommations
 * @property Consommation[] $consommations_auxiliaires
 */
final class Systeme
{
    public function __construct(
        public readonly Id $id,
        public readonly Id $generateur_id,
        public readonly TypeDistribution $type_distribution,
        public readonly ?Reseau $reseau,
        public readonly bool $position_volume_chauffe,
        public readonly ?float $rdim,
        public readonly ?Performance $performance,
        public readonly array $emetteurs,
        public readonly array $rendements,
        public readonly array $consommations,
        public readonly array $consommations_auxiliaires,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
            id: $entity->id(),
            generateur_id: $entity->generateur()->id(),
            type_distribution: $entity->type_distribution(),
            reseau: $entity->reseau(),
            position_volume_chauffe: $entity->position_volume_chauffe(),
            rdim: $entity->rdim(),
            performance: $entity->generateur()->performance(),
            emetteurs: Emetteur::from_collection($entity->emetteurs()),
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
