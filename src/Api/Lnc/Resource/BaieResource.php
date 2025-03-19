<?php

namespace App\Api\Lnc\Resource;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Lnc\Entity\{Baie as Entity, BaieCollection as EntityCollection};
use App\Domain\Lnc\Enum\{EtatIsolation, TypeBaie};
use App\Domain\Lnc\ValueObject\{EnsoleillementBaie, Menuiserie, Position};

final class BaieResource
{
    public function __construct(
        public readonly Id $id,
        public readonly string $description,
        public readonly Position $position,
        public readonly TypeBaie $type,
        public readonly EtatIsolation $etat_isolation,
        public readonly float $surface,
        public readonly float $inclinaison,
        public readonly ?Menuiserie $menuiserie,
        public readonly ?float $aiu,
        public readonly ?float $aue,
        /** @var EnsoleillementBaie[] */
        public readonly array $ensoleillements,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
            id: $entity->id(),
            description: $entity->description(),
            position: $entity->position(),
            type: $entity->type(),
            etat_isolation: $entity->etat_isolation(),
            surface: $entity->surface(),
            inclinaison: $entity->inclinaison(),
            menuiserie: $entity->menuiserie(),
            aiu: $entity->aiu(),
            aue: $entity->aue(),
            ensoleillements: $entity->ensoleillement()?->values() ?? [],
        );
    }

    /** @return self[] */
    public static function from_collection(EntityCollection $collection): array
    {
        return $collection->map(fn(Entity $entity) => self::from($entity))->values();
    }
}
