<?php

namespace App\Domain\Ecs\Table;

use App\Domain\Ecs\Enum\{BouclageReseau, TypeInstallation};

interface RdRepository
{
    public function find(int $id): ?Rd;
    public function find_by(
        TypeInstallation $type_installation,
        ?BouclageReseau $bouclage_reseau,
        ?bool $alimentation_contigue,
        ?bool $position_volume_habitable,
    ): ?Rd;
}
