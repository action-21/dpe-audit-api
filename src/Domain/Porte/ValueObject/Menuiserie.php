<?php

namespace App\Domain\Porte\ValueObject;

use App\Domain\Porte\Enum\{NatureMenuiserie, TypePose};
use Webmozart\Assert\Assert;

final class Menuiserie
{
    public function __construct(
        public readonly NatureMenuiserie $nature_menuiserie,
        public readonly TypePose $type_pose,
        public readonly bool $presence_joint,
        public readonly bool $presence_retour_isolation,
        public readonly ?int $largeur_dormant,
    ) {}

    public static function create(
        NatureMenuiserie $nature_menuiserie,
        TypePose $type_pose,
        bool $presence_joint,
        bool $presence_retour_isolation,
        ?int $largeur_dormant,
    ): self {
        Assert::nullOrGreaterThanEq($largeur_dormant, 0);

        return new self(
            nature_menuiserie: $nature_menuiserie,
            type_pose: $type_pose,
            presence_joint: $presence_joint,
            presence_retour_isolation: $presence_retour_isolation,
            largeur_dormant: $largeur_dormant,
        );
    }
}
