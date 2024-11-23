<?php

namespace App\Domain\Lnc\ValueObject;

use App\Domain\Lnc\Enum\{EtatIsolation, NatureMenuiserie, TypeVitrage};

final class Menuiserie
{
    public function __construct(
        public readonly NatureMenuiserie $nature_menuiserie,
        public readonly TypeVitrage $type_vitrage,
        public readonly ?bool $presence_rupteur_pont_thermique,
    ) {}

    public static function create(
        NatureMenuiserie $nature_menuiserie,
        TypeVitrage $type_vitrage,
        ?bool $presence_rupteur_pont_thermique,
    ): self {
        return new self(
            nature_menuiserie: $nature_menuiserie,
            type_vitrage: $type_vitrage,
            presence_rupteur_pont_thermique: $presence_rupteur_pont_thermique,
        );
    }
    public function etat_isolation(): EtatIsolation
    {
        return $this->type_vitrage?->etat_isolation() ?? EtatIsolation::NON_ISOLE;
    }
}
