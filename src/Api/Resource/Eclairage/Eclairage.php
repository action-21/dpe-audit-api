<?php

namespace App\Api\Resource\Eclairage;

use App\Domain\Common\Type\Id;
use App\Domain\Common\ValueObject\Consommation;
use App\Domain\Eclairage\Eclairage as Entity;
use ApiPlatform\Metadata\{ApiProperty, ApiResource};

final class Eclairage
{
    public function __construct(
        #[ApiProperty(identifier: true, readable: false, writable: false)]
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
