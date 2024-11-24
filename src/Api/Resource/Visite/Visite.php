<?php

namespace App\Api\Resource\Visite;

use App\Domain\Common\Type\Id;
use App\Domain\Visite\{Visite as Entity};
use ApiPlatform\Metadata\{ApiProperty, ApiResource};

final class Visite
{
    public function __construct(
        #[ApiProperty(identifier: true, readable: false, writable: false)]
        public readonly Id $audit_id,
        /** @var Logement[] */
        public readonly array $logements,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
            audit_id: $entity->audit()->id(),
            logements: Logement::from_collection($entity->logements()),
        );
    }
}
