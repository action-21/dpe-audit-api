<?php

namespace App\Api\Enveloppe\Model;

use App\Api\Enveloppe\Model\Porte\{Data, Menuiserie, Position, Vitrage};
use App\Domain\Enveloppe\Entity\{Porte as Entity, PorteCollection as EntityCollection};
use App\Domain\Enveloppe\Enum\{EtatIsolation, TypePose};
use App\Domain\Enveloppe\Enum\Porte\Materiau;
use App\Services\Validator\Constraints as DpeAssert;
use Symfony\Component\Validator\Constraints as Assert;

final class Porte
{
    public function __construct(
        public string $id,

        public string $description,

        public TypePose $type_pose,

        public ?EtatIsolation $isolation,

        public ?Materiau $materiau,

        public ?bool $presence_sas,

        #[DpeAssert\Annee]
        public ?int $annee_installation,

        #[Assert\Positive]
        public ?float $u,

        #[Assert\Valid]
        public Position $position,

        #[Assert\Valid]
        public Vitrage $vitrage,

        #[Assert\Valid]
        public Menuiserie $menuiserie,

        public ?Data $data,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
            id: $entity->id(),
            description: $entity->description(),
            type_pose: $entity->type_pose(),
            isolation: $entity->isolation(),
            materiau: $entity->materiau(),
            presence_sas: $entity->presence_sas(),
            annee_installation: $entity->annee_installation()?->value,
            u: $entity->u(),
            position: Position::from($entity),
            vitrage: Vitrage::from($entity),
            menuiserie: Menuiserie::from($entity),
            data: Data::from($entity),
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
