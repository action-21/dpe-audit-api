<?php

namespace App\Api\Enveloppe\Model\Baie;

use App\Domain\Enveloppe\Entity\Baie\DoubleFenetre as Entity;
use App\Domain\Enveloppe\Enum\Baie\{Materiau, TypeBaie};
use App\Domain\Enveloppe\Enum\TypePose;
use Symfony\Component\Validator\Constraints as Assert;

final class DoubleFenetre
{
    public function __construct(
        public readonly TypeBaie $type_baie,

        public readonly ?TypePose $type_pose,

        public readonly ?Materiau $materiau,

        public readonly ?bool $presence_soubassement,

        #[Assert\Positive]
        public readonly ?float $ug,

        #[Assert\Positive]
        public readonly ?float $uw,

        #[Assert\Positive]
        public readonly ?float $sw,

        #[Assert\Valid]
        public readonly ?Vitrage $vitrage,

        #[Assert\Valid]
        public readonly ?Menuiserie $menuiserie,

        public readonly ?DoubleFenetreData $data,
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
