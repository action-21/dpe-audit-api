<?php

namespace App\Application\Refroidissement\View;

use App\Domain\Common\Type\Id;
use App\Domain\Common\ValueObject\{Besoin, Consommation};
use App\Domain\Refroidissement\Refroidissement as Entity;

/**
 * @property Generateur[] $generateurs
 * @property Installation[] $installations
 * @property Besoin[] $besoins
 * @property Consommation[] $consommations
 */
final class Refroidissement
{
    public function __construct(
        public readonly Id $audit_id,
        public readonly array $generateurs,
        public readonly array $installations,
        public readonly array $besoins,
        public readonly array $consommations,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
            audit_id: $entity->audit()->id(),
            generateurs: Generateur::from_collection($entity->generateurs()),
            installations: Installation::from_collection($entity->installations()),
            besoins: $entity->besoins()?->values() ?? [],
            consommations: $entity->installations()->consommations()?->values() ?? [],
        );
    }
}
