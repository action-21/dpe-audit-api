<?php

namespace App\Api\Chauffage\Model;

use App\Domain\Chauffage\Entity\{Installation as Entity, InstallationCollection as EntityCollection};
use Symfony\Component\Validator\Constraints as Assert;

final class Installation
{
    public function __construct(
        #[Assert\Uuid]
        public readonly string $id,

        public readonly string $description,

        #[Assert\Positive]
        public readonly float $surface,

        public readonly bool $comptage_individuel,

        #[Assert\Valid]
        public readonly ?Solaire $solaire_thermique,

        #[Assert\Valid]
        public readonly Regulation $regulation_centrale,

        #[Assert\Valid]
        public readonly Regulation $regulation_terminale,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
            id: $entity->id(),
            description: $entity->description(),
            surface: $entity->surface(),
            comptage_individuel: $entity->comptage_individuel(),
            solaire_thermique: $entity->solaire_thermique() ? Solaire::from($entity) : null,
            regulation_centrale: Regulation::from($entity->regulation_centrale()),
            regulation_terminale: Regulation::from($entity->regulation_terminale()),
        );
    }

    /**
     * @return self[]
     */
    public static function from_collection(EntityCollection $collection): array
    {
        return $collection->map(fn(Entity $entity) => self::from($entity))->to_array();
    }
}
