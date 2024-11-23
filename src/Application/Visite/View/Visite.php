<?php

namespace App\Application\Visite\View;

use App\Domain\Visite\{Visite as Entity};

/**
 * @property Logement[] $logements
 */
final class Visite
{
    public function __construct(
        public readonly array $logements,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
            logements: Logement::from_collection($entity->logements()),
        );
    }
}
