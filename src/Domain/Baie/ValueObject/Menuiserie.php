<?php

namespace App\Domain\Baie\ValueObject;

use App\Domain\Baie\Enum\{NatureMenuiserie, TypePose};
use Webmozart\Assert\Assert;

final class Menuiserie
{
    public function __construct(
        public readonly NatureMenuiserie $nature,
        public readonly TypePose $type_pose,
        public readonly bool $presence_joint,
        public readonly bool $presence_retour_isolation,
        public readonly ?int $largeur_dormant,
        public readonly ?bool $presence_rupteur_pont_thermique = null,
    ) {}

    public static function create(
        NatureMenuiserie $nature,
        TypePose $type_pose,
        bool $presence_joint,
        bool $presence_retour_isolation,
        int $largeur_dormant,
        ?bool $presence_rupteur_pont_thermique
    ): self {
        Assert::greaterThan($largeur_dormant, 0);

        return new self(
            nature: $nature,
            type_pose: $type_pose,
            presence_joint: $presence_joint,
            presence_retour_isolation: $presence_retour_isolation,
            largeur_dormant: $largeur_dormant,
            presence_rupteur_pont_thermique: $presence_rupteur_pont_thermique,
        );
    }
}
