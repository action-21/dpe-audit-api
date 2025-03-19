<?php

namespace App\Api\PontThermique\Resource;

use App\Domain\Common\ValueObject\Id;
use App\Domain\PontThermique\{PontThermique as Entity, PontThermiqueCollection as EntityCollection};
use App\Domain\PontThermique\Enum\{TypeIsolation, TypePose};
use App\Domain\PontThermique\ValueObject\{Liaison, Performance};

final class PontThermiqueResource
{
    public function __construct(
        public readonly Id $id,
        public readonly string $description,
        public readonly float $longueur,
        public readonly Liaison $liaison,
        public readonly TypeIsolation $type_isolation_mur,
        public readonly ?TypeIsolation $type_isolation_plancher,
        public readonly ?TypePose $type_pose_ouverture,
        public readonly ?bool $presence_retour_isolation,
        public readonly ?int $largeur_dormant,
        public readonly ?float $kpt,
        public readonly ?Performance $performance,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
            id: $entity->id(),
            description: $entity->description(),
            longueur: $entity->longueur(),
            liaison: $entity->liaison(),
            type_isolation_mur: $entity->type_isolation_mur(),
            type_isolation_plancher: $entity->type_isolation_plancher_bas() ?? $entity->type_isolation_plancher_haut(),
            type_pose_ouverture: $entity->type_pose_ouverture(),
            presence_retour_isolation: $entity->presence_retour_isolation(),
            largeur_dormant: $entity->largeur_dormant(),
            kpt: $entity->kpt(),
            performance: $entity->performance(),
        );
    }

    /** @return self[] */
    public static function from_collection(EntityCollection $collection): array
    {
        return $collection->map(fn(Entity $entity) => self::from($entity))->values();
    }
}
