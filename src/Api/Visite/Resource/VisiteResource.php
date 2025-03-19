<?php

namespace App\Api\Visite\Resource;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Visite\{Visite as Entity};

final class VisiteResource
{
    public function __construct(
        public readonly Id $audit_id,
        /** @var LogementResource[] */
        public readonly array $logements,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
            audit_id: $entity->audit()->id(),
            logements: LogementResource::from_collection($entity->logements()),
        );
    }
}
