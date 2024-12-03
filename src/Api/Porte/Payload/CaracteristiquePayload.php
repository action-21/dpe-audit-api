<?php

namespace App\Api\Porte\Payload;

use App\Domain\Porte\Enum\{EtatIsolation, NatureMenuiserie, TypePose, TypeVitrage};
use Symfony\Component\Validator\Constraints as Assert;

final class CaracteristiquePayload
{
    public function __construct(
        #[Assert\Positive]
        public float $surface,
        public EtatIsolation $isolation,
        public NatureMenuiserie $nature_menuiserie,
        public TypePose $type_pose,
        #[Assert\PositiveOrZero]
        #[Assert\LessThanOrEqual(60)]
        public int $taux_vitrage,
        #[Assert\PositiveOrZero]
        public ?int $largeur_dormant,
        public bool $presence_sas,
        public bool $presence_joint,
        public bool $presence_retour_isolation,
        public ?int $annee_installation,
        public ?TypeVitrage $type_vitrage,
        public ?float $u,
    ) {}
}
