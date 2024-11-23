<?php

namespace App\Application\Lnc\View;

use App\Domain\Common\Type\Id;
use App\Domain\Lnc\Entity\{Paroi as Entity, ParoiCollection as EntityCollection};
use App\Domain\Lnc\Enum\EtatIsolation;
use App\Domain\Lnc\ValueObject\Position;

final class Paroi
{
    public function __construct(
        public readonly Id $id,
        public readonly string $description,
        public readonly Position $position,
        public readonly float $surface,
        public readonly EtatIsolation $etat_isolation,
        public readonly ?float $aiu,
        public readonly ?float $aue,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
            id: $entity->id(),
            description: $entity->description(),
            position: $entity->position(),
            surface: $entity->surface(),
            etat_isolation: $entity->etat_isolation(),
            aiu: $entity->aiu(),
            aue: $entity->aue(),
        );
    }

    /** @return self[] */
    public static function from_collection(EntityCollection $collection): array
    {
        return $collection->map(fn(Entity $entity) => self::from($entity))->values();
    }
}
