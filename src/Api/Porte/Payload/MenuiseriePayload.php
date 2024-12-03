<?php

namespace App\Api\Porte\Payload;

use App\Domain\Porte\Enum\{NatureMenuiserie, TypePose};
use App\Domain\Porte\ValueObject\Menuiserie;
use Symfony\Component\Validator\Constraints as Assert;

final class MenuiseriePayload
{
    public function __construct(
        public NatureMenuiserie $nature_menuiserie,
        public TypePose $type_pose,
        public bool $presence_joint,
        public bool $presence_retour_isolation,
        #[Assert\PositiveOrZero]
        public ?int $largeur_dormant,
    ) {}

    public function to(): Menuiserie
    {
        return Menuiserie::create(
            nature_menuiserie: $this->nature_menuiserie,
            type_pose: $this->type_pose,
            presence_joint: $this->presence_joint,
            presence_retour_isolation: $this->presence_retour_isolation,
            largeur_dormant: $this->largeur_dormant,
        );
    }
}
