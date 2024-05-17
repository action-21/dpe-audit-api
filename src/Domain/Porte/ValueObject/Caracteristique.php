<?php

namespace App\Domain\Porte\ValueObject;

use App\Domain\Porte\ValueObject\{LargeurDormant, Surface};
use App\Domain\Porte\Enum\{NatureMenuiserie, TypePorte, TypePose};

final class Caracteristique
{
    public function __construct(
        public readonly Surface $surface,
        public readonly TypePose $type_pose,
        public readonly NatureMenuiserie $nature_menuiserie,
        public readonly TypePorte $type_porte,
        public readonly bool $presence_joint,
        public readonly ?bool $presence_retour_isolation = null,
        public readonly ?LargeurDormant $largeur_dormant = null,
        public readonly ?Uporte $uporte = null,
    ) {
    }

    public static function create(
        Surface $surface,
        TypePose $type_pose,
        NatureMenuiserie $nature_menuiserie,
        TypePorte $type_porte,
        bool $presence_joint,
        ?bool $presence_retour_isolation,
        ?LargeurDormant $largeur_dormant,
        ?Uporte $uporte,
    ): self {
        if (!\in_array($type_porte, TypePorte::cases_by_nature_menuiserie($nature_menuiserie))) {
            throw new \DomainException('Type de porte non applicable');
        }
        return new self(
            surface: $surface,
            type_pose: $type_pose,
            nature_menuiserie: $nature_menuiserie,
            type_porte: $type_porte,
            presence_joint: $presence_joint,
            presence_retour_isolation: $presence_retour_isolation,
            largeur_dormant: $largeur_dormant,
            uporte: $uporte,
        );
    }
}
