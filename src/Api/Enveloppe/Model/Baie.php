<?php

namespace App\Api\Enveloppe\Model;

use App\Api\Enveloppe\Model\Baie\{DoubleFenetre, Data, MasqueLointain, MasqueProche, Menuiserie, Position, Vitrage};
use App\Domain\Enveloppe\Entity\{Baie as Entity, BaieCollection as EntityCollection};
use App\Domain\Enveloppe\Enum\Baie\{Materiau, TypeBaie, TypeFermeture};
use App\Domain\Enveloppe\Enum\TypePose;
use App\Services\Validator\Constraints as DpeAssert;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @property MasqueLointain[] $masques_lointains
 * @property MasqueProche[] $masques_proches
 */
final class Baie
{
    public function __construct(
        #[Assert\Uuid]
        public readonly string $id,

        public readonly string $description,

        public readonly TypeBaie $type_baie,

        public readonly bool $presence_protection_solaire,

        public readonly TypeFermeture $type_fermeture,

        public readonly ?Materiau $materiau,

        public readonly ?TypePose $type_pose,

        public readonly ?bool $presence_soubassement,

        #[DpeAssert\Annee]
        public readonly ?int $annee_installation,

        #[Assert\Positive]
        public readonly ?float $ug,

        #[Assert\Positive]
        public readonly ?float $uw,

        #[Assert\Positive]
        public readonly ?float $ujn,

        #[Assert\Positive]
        public readonly ?float $sw,

        #[Assert\Valid]
        public readonly Position $position,

        #[Assert\Valid]
        public readonly ?Menuiserie $menuiserie,

        #[Assert\Valid]
        public readonly ?Vitrage $vitrage,

        public readonly ?DoubleFenetre $double_fenetre,

        /** @var MasqueLointain[] */
        public readonly array $masques_lointains,

        /** @var MasqueProche[] */
        public readonly array $masques_proches,

        public readonly ?Data $data,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
            id: $entity->id(),
            description: $entity->description(),
            type_baie: $entity->type_baie(),
            presence_protection_solaire: $entity->presence_protection_solaire(),
            type_fermeture: $entity->type_fermeture(),
            materiau: $entity->materiau(),
            type_pose: $entity->type_pose(),
            presence_soubassement: $entity->presence_soubassement(),
            annee_installation: $entity->annee_installation()?->value,
            ug: $entity->performance()->ug,
            uw: $entity->performance()->uw,
            ujn: $entity->performance()->ujn,
            sw: $entity->performance()->sw?->value(),
            position: Position::from($entity),
            menuiserie: Menuiserie::from($entity),
            vitrage: Vitrage::from($entity),
            double_fenetre: $entity->double_fenetre() ? DoubleFenetre::from($entity->double_fenetre()) : null,
            masques_lointains: MasqueLointain::from_collection($entity->masques_lointains()),
            masques_proches: MasqueProche::from_collection($entity->masques_proches()),
            data: Data::from($entity),
        );
    }

    public static function from_collection(EntityCollection $collection): array
    {
        return $collection->map(fn(Entity $entity) => self::from($entity))->to_array();
    }
}
