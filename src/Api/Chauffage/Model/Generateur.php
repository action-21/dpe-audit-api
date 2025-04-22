<?php

namespace App\Api\Chauffage\Model;

use App\Domain\Chauffage\Entity\{Generateur as Entity, GenerateurCollection as EntityCollection};
use App\Domain\Chauffage\Enum\{EnergieGenerateur, LabelGenerateur, ModeCombustion, TypeGenerateur, TypeChaudiere};
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

        public readonly ?EnergieGenerateur $energie_partie_chaudiere,

        public readonly bool $generateur_collectif,

        public readonly bool $position_volume_chauffe,

        public readonly bool $generateur_multi_batiment,

        #[DpeAssert\Annee]
        public readonly ?int $annee_installation,

        #[Assert\Positive]
        public readonly ?float $pn,

        #[Assert\Positive]
        public readonly ?float $scop,

        public readonly ?LabelGenerateur $label,

        public readonly ?TypeChaudiere $type_chaudiere,

        #[Assert\PositiveOrZero]
        public readonly ?int $priorite_cascade,

        public readonly ?ModeCombustion $mode_combustion,

        public readonly ?bool $presence_ventouse,

        public readonly ?bool $presence_regulation_combustion,

        #[Assert\PositiveOrZero]
        public readonly ?float $pveilleuse,

        #[Assert\Positive]
        public readonly ?float $qp0,

        #[Assert\Positive]
        #[Assert\LessThanOrEqual(150)]
        public readonly ?float $rpn,

        #[Assert\Positive]
        #[Assert\LessThanOrEqual(150)]
        public readonly ?float $rpint,

        #[Assert\Positive]
        #[Assert\LessThanOrEqual(70)]
        public readonly ?float $tfonc30,

        #[Assert\Positive]
        #[Assert\LessThanOrEqual(70)]
        public readonly ?float $tfonc100,

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
            energie_partie_chaudiere: $entity->energie_partie_chaudiere(),
            generateur_collectif: $entity->position()->generateur_collectif,
            position_volume_chauffe: $entity->position()->position_volume_chauffe,
            generateur_multi_batiment: $entity->position()->generateur_multi_batiment,
            annee_installation: $entity->annee_installation()?->value,
            pn: $entity->signaletique()->pn,
            scop: $entity->signaletique()->scop,
            label: $entity->signaletique()->label,
            type_chaudiere: $entity->signaletique()->type_chaudiere,
            priorite_cascade: $entity->signaletique()->priorite_cascade,
            mode_combustion: $entity->combustion()?->mode_combustion,
            presence_ventouse: $entity->combustion()?->presence_ventouse,
            presence_regulation_combustion: $entity->combustion()?->presence_regulation_combustion,
            pveilleuse: $entity->combustion()?->pveilleuse,
            qp0: $entity->combustion()?->qp0,
            rpn: $entity->combustion()?->rpn?->value(),
            rpint: $entity->combustion()?->rpint?->value(),
            tfonc30: $entity->combustion()?->tfonc30,
            tfonc100: $entity->combustion()?->tfonc100,
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
