<?php

namespace App\Api\Resource\Chauffage;

use App\Domain\Common\Type\Id;
use App\Domain\Common\ValueObject\Consommation;
use App\Domain\Chauffage\Entity\{Systeme as Entity, SystemeCollection as EntityCollection};
use App\Domain\Chauffage\Enum\TypeDistribution;
use App\Domain\Chauffage\ValueObject\{Performance, Rendement, Reseau};
use ApiPlatform\Metadata\{ApiProperty, ApiResource};

final class Systeme
{
    public function __construct(
        #[ApiProperty(identifier: true, readable: false, writable: false)]
        public readonly Id $id,
        public readonly Id $generateur_id,
        public readonly TypeDistribution $type_distribution,
        public readonly ?Reseau $reseau,
        public readonly bool $position_volume_chauffe,
        public readonly ?float $rdim,
        public readonly ?Performance $performance,
        /** @var Emetteur[] */
        public readonly array $emetteurs,
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
