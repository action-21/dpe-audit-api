<?php

namespace App\Domain\Baie\Table;

use App\Domain\Baie\Enum\{NatureMenuiserie, TypeBaie, TypePose, TypeVitrage};

interface SwRepository
{
    public function find(int $id): ?Sw;
    public function find_by(
        TypeBaie $type_baie,
        NatureMenuiserie $nature_menuiserie,
        ?TypePose $type_pose,
        ?TypeVitrage $type_vitrage,
    ): ?Sw;
}
