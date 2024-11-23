<?php

namespace App\Application\Ventilation\View;

use App\Domain\Common\Type\Id;
use App\Domain\Common\ValueObject\Consommation;
use App\Domain\Ventilation\Ventilation as Entity;

/**
 * @property Generateur[] $generateurs
 * @property Installation[] $installations
 * @property Consommation[] $consommations
 */
final class Ventilation
{
    public function __construct(
        public readonly Id $audit_id,
        public readonly array $generateurs,
        public readonly array $installations,
        public readonly array $consommations,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
            audit_id: $entity->audit()->id(),
            generateurs: Generateur::from_collection($entity->generateurs()),
            installations: Installation::from_collection($entity->installations()),
            consommations: $entity->installations()->consommations()?->values() ?? [],
        );
    }
}
