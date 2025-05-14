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
        public string $id,

        public string $description,

        public TypeBaie $type_baie,

        public bool $presence_protection_solaire,

        public TypeFermeture $type_fermeture,

        public ?Materiau $materiau,

        public ?TypePose $type_pose,

        public ?bool $presence_soubassement,

        #[DpeAssert\Annee]
        public ?int $annee_installation,

        #[Assert\Positive]
        public ?float $ug,

        #[Assert\Positive]
        public ?float $uw,

        #[Assert\Positive]
        public ?float $ujn,

        #[Assert\Positive]
        public ?float $sw,

        #[Assert\Valid]
        public Position $position,

        #[Assert\Valid]
        public ?Menuiserie $menuiserie,

        #[Assert\Valid]
        public ?Vitrage $vitrage,

        public ?DoubleFenetre $double_fenetre,

        /** @var MasqueLointain[] */
        public array $masques_lointains,

        /** @var MasqueProche[] */
        public array $masques_proches,

        public ?Data $data,
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
