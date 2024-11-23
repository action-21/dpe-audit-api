<?php

namespace App\Application\Ventilation\View;

use App\Domain\Common\Type\Id;
use App\Domain\Ventilation\Enum\{TypeGenerateur, TypeVentilation};
use App\Domain\Ventilation\Entity\{Generateur as Entity, GenerateurCollection as EntityCollection};

final class Generateur
{
    public function __construct(
        public readonly Id $id,
        public readonly string $description,
        public readonly TypeVentilation $type_ventilation,
        public readonly TypeGenerateur $type,
        public readonly bool $presence_echangeur_thermique,
        public readonly bool $generateur_collectif,
        public readonly ?int $annee_installation,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
            id: $entity->id(),
            description: $entity->description(),
            type_ventilation: $entity->type_ventilation(),
            type: $entity->type(),
            presence_echangeur_thermique: $entity->presence_echangeur_thermique(),
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
