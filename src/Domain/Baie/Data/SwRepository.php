<?php

namespace App\Domain\Baie\Data;

use App\Domain\Baie\Enum\{NatureMenuiserie, TypeBaie, TypePose, TypeVitrage};

interface SwRepository
{
    public function find_by(
        TypeBaie $type_baie,
        ?bool $presence_soubassement,
        ?NatureMenuiserie $nature_menuiserie,
        ?TypeVitrage $type_vitrage,
        ?TypePose $type_pose,
    ): ?Sw;
}
