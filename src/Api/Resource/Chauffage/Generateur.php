<?php

namespace App\Api\Resource\Chauffage;

use App\Domain\Common\Type\Id;
use App\Domain\Chauffage\Entity\{Generateur as Entity, GenerateurCollection as EntityCollection};
use App\Domain\Chauffage\Enum\{CategorieGenerateur, EnergieGenerateur, TypeGenerateur};
use App\Domain\Chauffage\ValueObject\{Performance, Perte, Signaletique};
use ApiPlatform\Metadata\{ApiProperty, ApiResource};

final class Generateur
{
    public function __construct(
        #[ApiProperty(identifier: true, readable: false, writable: false)]
        public readonly Id $id,
        public readonly ?Id $generateur_mixte_id,
        public readonly ?Id $reseau_chaleur_id,
        public readonly string $description,
        public readonly CategorieGenerateur $categorie,
        public readonly TypeGenerateur $type,
        public readonly EnergieGenerateur $energie,
        public readonly bool $position_volume_chauffe,
        public readonly bool $generateur_collectif,
        public readonly Signaletique $signaletique,
        public readonly ?int $annee_installation,
        public readonly ?TypeGenerateur $type_partie_chaudiere,
        public readonly ?EnergieGenerateur $energie_partie_chaudiere,
        public readonly ?Performance $performance,
        /** @var Perte[] */
        public readonly array $pertes_generation,
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
            position_volume_chauffe: $entity->position_volume_chauffe(),
            generateur_collectif: $entity->generateur_collectif(),
            signaletique: $entity->signaletique(),
            annee_installation: $entity->annee_installation(),
            type_partie_chaudiere: $entity->type_partie_chaudiere(),
            energie_partie_chaudiere: $entity->energie_partie_chaudiere(),
            performance: $entity->performance(),
            pertes_generation: $entity->pertes_generation()?->values() ?? [],
        );
    }

    /** @return self[] */
    public static function from_collection(EntityCollection $collection): array
    {
        return $collection->map(fn(Entity $entity) => self::from($entity))->values();
    }
}
