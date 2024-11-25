<?php

namespace App\Api\Eclairage\Resource;

use App\Domain\Common\Type\Id;
use App\Domain\Common\ValueObject\Consommation;
use App\Domain\Eclairage\Eclairage as Entity;

final class EclairageResource
{
    public function __construct(
        public readonly Id $audit_id,
        /** @var Consommation[] */
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
