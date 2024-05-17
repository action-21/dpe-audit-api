<?php

namespace App\Domain\PlancherHaut\Table;

use App\Domain\Batiment\Enum\ZoneClimatique;
use App\Domain\PlancherHaut\Enum\ConfigurationPlancherHaut;

interface UphRepository
{
    public function find(int $id): ?Uph;
    public function find_by(
        ZoneClimatique $zone_climatique,
        ConfigurationPlancherHaut $configuration_plancher_haut,
        int $annee_construction_isolation,
        bool $effet_joule,
    ): ?Uph;
}
