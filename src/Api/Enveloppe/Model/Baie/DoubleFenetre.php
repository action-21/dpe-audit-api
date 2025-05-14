<?php

namespace App\Api\Enveloppe\Model\Baie;

use App\Domain\Enveloppe\Entity\Baie\DoubleFenetre as Entity;
use App\Domain\Enveloppe\Enum\Baie\{Materiau, TypeBaie};
use App\Domain\Enveloppe\Enum\TypePose;
use Symfony\Component\Validator\Constraints as Assert;

final class DoubleFenetre
{
    public function __construct(
        public TypeBaie $type_baie,

        public ?TypePose $type_pose,

        public ?Materiau $materiau,

        public ?bool $presence_soubassement,

        #[Assert\Positive]
        public ?float $ug,

        #[Assert\Positive]
        public ?float $uw,

        #[Assert\Positive]
        public ?float $sw,

        #[Assert\Valid]
        public ?Vitrage $vitrage,

        #[Assert\Valid]
        public ?Menuiserie $menuiserie,

        public ?DoubleFenetreData $data,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
            type_baie: $entity->type_baie(),
            type_pose: $entity->type_pose(),
            materiau: $entity->materiau(),
            presence_soubassement: $entity->presence_soubassement(),
            ug: $entity->performance()->ug,
            uw: $entity->performance()->uw,
            sw: $entity->performance()->sw?->value(),
            vitrage: ($value = $entity->vitrage()) ? Vitrage::from_value($value) : null,
            menuiserie: ($value = $entity->menuiserie()) ? Menuiserie::from_value($value) : null,
            data: DoubleFenetreData::from($entity),
        );
    }
}
