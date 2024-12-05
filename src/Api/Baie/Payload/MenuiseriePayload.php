<?php

namespace App\Api\Baie\Payload;

use App\Domain\Baie\Enum\{NatureMenuiserie, TypePose};
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
}
