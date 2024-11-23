<?php

namespace App\Application\Eclairage\View;

use App\Domain\Common\Type\Id;
use App\Domain\Common\ValueObject\Consommation;
use App\Domain\Eclairage\Eclairage as Entity;

/**
 * @property Consommation[] $consommations
 */
final class Eclairage
{
    public function __construct(
        public readonly Id $audit_id,
        public readonly array $consommations,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
            audit_id: $entity->audit()->id(),
            consommations: $entity->consommations()?->values() ?? [],
        );
    }
}
