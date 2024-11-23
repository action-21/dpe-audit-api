<?php

namespace App\Domain\Lnc\Data;

use App\Domain\Lnc\Enum\{NatureMenuiserie, TypeBaie, TypeVitrage};

interface TRepository
{
    public function find_by(
        TypeBaie $type_baie,
        ?NatureMenuiserie $nature_menuiserie,
        ?TypeVitrage $type_vitrage,
        ?bool $presence_rupteur_pont_thermique,
    ): ?T;
}
