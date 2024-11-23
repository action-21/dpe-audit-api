<?php

namespace App\Domain\Baie\Data;

use App\Domain\Baie\Enum\{NatureMenuiserie, TypeBaie};

interface UwRepository
{
    public function search_by(
        TypeBaie $type_baie,
        ?NatureMenuiserie $nature_menuiserie,
        ?bool $presence_soubassement,
        ?bool $presence_rupteur_pont_thermique,
    ): UwCollection;
}
