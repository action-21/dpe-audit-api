<?php

namespace App\Application\Chauffage\View;

use App\Domain\Common\Type\Id;
use App\Domain\Common\ValueObject\{Besoin, Consommation};
use App\Domain\Chauffage\Chauffage as Entity;

/**
 * @property Generateur[] $generateurs
 * @property Emetteur[] $emetteurs
 * @property Installation[] $installations
 * @property Besoin[] $besoins_bruts
 * @property Besoin[] $besoins
 * @property Consommation[] $consommations
 */
final class Chauffage
{
    public function __construct(
        public readonly Id $audit_id,
        public readonly array $generateurs,
        public readonly array $emetteurs,
        public readonly array $installations,
        public readonly array $besoins_bruts,
        public readonly array $besoins,
        public readonly array $consommations,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
            audit_id: $entity->audit()->id(),
            generateurs: Generateur::from_collection($entity->generateurs()),
            emetteurs: Emetteur::from_collection($entity->emetteurs()),
            installations: Installation::from_collection($entity->installations()),
            besoins_bruts: $entity->besoins_bruts()?->values() ?? [],
            besoins: $entity->besoins()?->values() ?? [],
            consommations: $entity->installations()->consommations()?->values() ?? [],
        );
    }
}
