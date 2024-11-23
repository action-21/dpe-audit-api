<?php

namespace App\Domain\Ecs\Data;

use App\Domain\Ecs\Enum\{BouclageReseau};

interface RdRepository
{
    public function find_by(
        bool $reseau_collectif,
        ?BouclageReseau $bouclage_reseau,
        ?bool $alimentation_contigue,
        ?bool $production_volume_habitable,
    ): ?Rd;
}
