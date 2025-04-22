<?php

namespace App\Api\Ecs\Model;

use App\Domain\Ecs\Entity\{Generateur as Entity, GenerateurCollection as EntityCollection};
use App\Domain\Ecs\Enum\{EnergieGenerateur, LabelGenerateur, ModeCombustion, TypeGenerateur, TypeChaudiere};
use App\Services\Validator\Constraints as DpeAssert;
use Symfony\Component\Validator\Constraints as Assert;

final class Generateur
{
    public function __construct(
        #[Assert\Uuid]
        public readonly string $id,

        public readonly string $description,

        public readonly TypeGenerateur $type,

        public readonly EnergieGenerateur $energie,

        #[Assert\PositiveOrZero]
        public readonly float $volume_stockage,

        public readonly bool $generateur_collectif,

        public readonly bool $position_volume_chauffe,

        #[DpeAssert\Annee]
        public readonly ?int $annee_installation,

        #[Assert\Positive]
        public readonly ?float $pn,

        #[Assert\Positive]
        public readonly ?float $cop,

        public readonly ?LabelGenerateur $label,

        public readonly ?TypeChaudiere $type_chaudiere,

        public readonly ?ModeCombustion $mode_combustion,

        public readonly ?bool $presence_ventouse,

        #[Assert\PositiveOrZero]
        public readonly ?float $pveilleuse,

        #[Assert\Positive]
        public readonly ?float $qp0,

        #[Assert\Positive]
        #[Assert\LessThanOrEqual(150)]
        public readonly ?float $rpn,

        #[Assert\Uuid]
        public readonly ?string $reseau_chaleur_id,

        #[Assert\Uuid]
        public readonly ?string $generateur_mixte_id,

        public readonly ?GenerateurData $data,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
            id: $entity->id(),
            description: $entity->description(),
            type: $entity->type(),
            energie: $entity->energie(),
            volume_stockage: $entity->signaletique()->volume_stockage,
            generateur_collectif: $entity->position()->generateur_collectif,
            position_volume_chauffe: $entity->position()->position_volume_chauffe,
            annee_installation: $entity->annee_installation()?->value,
            pn: $entity->signaletique()->pn,
            cop: $entity->signaletique()->cop,
            label: $entity->signaletique()->label,
            type_chaudiere: $entity->signaletique()->type_chaudiere,
            mode_combustion: $entity->combustion()?->mode_combustion,
            presence_ventouse: $entity->combustion()?->presence_ventouse,
            pveilleuse: $entity->combustion()?->pveilleuse,
            qp0: $entity->combustion()?->qp0,
            rpn: $entity->combustion()?->rpn?->value(),
            generateur_mixte_id: $entity->position()->generateur_mixte_id,
            reseau_chaleur_id: $entity->position()->reseau_chaleur?->id(),
            data: GenerateurData::from($entity),
        );
    }

    /**
     * @return self[]
     */
    public static function from_collection(EntityCollection $collection): array
    {
        return $collection->map(fn(Entity $entity) => self::from($entity))->to_array();
    }
}
