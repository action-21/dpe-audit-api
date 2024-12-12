<?php

namespace App\Api\Baie\Payload;

use App\Domain\Baie\Enum\{NatureMenuiserie, TypePose};
use App\Domain\Baie\ValueObject\Menuiserie;
use Symfony\Component\Validator\Constraints as Assert;

final class MenuiseriePayload
{
    public function __construct(
        public NatureMenuiserie $nature,
        public TypePose $type_pose,
        public bool $presence_joint,
        public bool $presence_retour_isolation,
        #[Assert\Positive]
        public ?int $largeur_dormant,
        public ?bool $presence_rupteur_pont_thermique,
    ) {}

    public function to(): Menuiserie
    {
        return Menuiserie::create(
            nature: $this->nature,
            type_pose: $this->type_pose,
            presence_joint: $this->presence_joint,
            presence_retour_isolation: $this->presence_retour_isolation,
            largeur_dormant: $this->largeur_dormant,
            presence_rupteur_pont_thermique: $this->presence_rupteur_pont_thermique,
        );
    }
}
