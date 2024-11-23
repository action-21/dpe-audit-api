<?php

namespace App\Domain\PlancherBas\Data;

use App\Domain\PlancherBas\Enum\Mitoyennete;

interface UeRepository
{
    public function search_by(Mitoyennete $mitoyennete, int $annee_construction): UeCollection;
}
