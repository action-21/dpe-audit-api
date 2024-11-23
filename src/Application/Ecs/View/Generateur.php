<?php

namespace App\Application\Ecs\View;

use App\Domain\Common\Type\Id;
use App\Domain\Ecs\Entity\{Generateur as Entity, GenerateurCollection as EntityCollection};
use App\Domain\Ecs\Enum\{CategorieGenerateur, EnergieGenerateur, TypeGenerateur, UsageEcs};
use App\Domain\Ecs\ValueObject\{Performance, Perte, Signaletique};

/**
 * @property Perte[] $pertes_generation
 * @property Perte[] $pertes_stockage
 */
final class Generateur
{
    public function __construct(
        public readonly Id $id,
        public readonly ?Id $generateur_mixte_id,
        public readonly ?Id $reseau_chaleur_id,
        public readonly string $description,
        public readonly CategorieGenerateur $categorie,
        public readonly TypeGenerateur $type,
        public readonly EnergieGenerateur $energie,
        public readonly int $volume_stockage,
        public readonly bool $position_volume_chauffe,
        public readonly bool $generateur_collectif,
        public readonly Signaletique $signaletique,
        public readonly ?int $annee_installation,
        public readonly ?Performance $performance,
        public readonly array $pertes_generation,
        public readonly array $pertes_stockage,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
            id: $entity->id(),
            generateur_mixte_id: $entity->generateur_mixte_id(),
            reseau_chaleur_id: $entity->reseau_chaleur_id(),
            description: $entity->description(),
            categorie: $entity->categorie(),
            type: $entity->type(),
            energie: $entity->energie(),
            volume_stockage: $entity->volume_stockage(),
            position_volume_chauffe: $entity->position_volume_chauffe(),
            generateur_collectif: $entity->generateur_collectif(),
            signaletique: $entity->signaletique(),
            annee_installation: $entity->annee_installation(),
            performance: $entity->performance(),
            pertes_generation: $entity->pertes_generation()?->values() ?? [],
            pertes_stockage: $entity->pertes_stockage()?->values() ?? [],
        );
    }

    /** @return self[] */
    public static function from_collection(EntityCollection $collection): array
    {
        return $collection->map(fn(Entity $entity) => self::from($entity))->values();
    }
}
