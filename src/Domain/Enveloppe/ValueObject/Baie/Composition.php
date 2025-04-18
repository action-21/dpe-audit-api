<?php

namespace App\Domain\Enveloppe\ValueObject\Baie;

use App\Domain\Enveloppe\Enum\Baie\{Materiau, TypeBaie};
use App\Domain\Enveloppe\Enum\TypePose;

final class Composition
{
    public function __construct(
        public readonly TypeBaie $type_baie,
        public readonly ?TypePose $type_pose = null,
        public readonly ?Materiau $materiau = null,
        public readonly ?bool $presence_soubassement = null,
        public readonly ?Vitrage $vitrage = null,
        public readonly ?Menuiserie $menuiserie = null,
    ) {}

    public static function create(
        TypeBaie $type_baie,
        ?TypePose $type_pose,
        ?Materiau $materiau,
        ?bool $presence_soubassement,
        ?Vitrage $vitrage,
        ?Menuiserie $menuiserie,
    ): self {
        return match (true) {
            $type_baie->is_paroi_vitree() => new self(
                type_baie: $type_baie,
                type_pose: null,
                materiau: null,
                vitrage: null,
                menuiserie: null,
            ),
            $type_baie->is_fenetre() => new self(
                type_baie: $type_baie,
                type_pose: $type_pose,
                materiau: $materiau,
                vitrage: $vitrage ?? Vitrage::create(),
                menuiserie: $menuiserie ?? Menuiserie::create(),
            ),
            $type_baie->is_porte_fenetre() => new self(
                type_baie: $type_baie,
                type_pose: $type_pose,
                materiau: $materiau,
                presence_soubassement: $presence_soubassement,
                vitrage: $vitrage ?? Vitrage::create(),
                menuiserie: $menuiserie ?? Menuiserie::create(),
            ),
        };
    }
}
