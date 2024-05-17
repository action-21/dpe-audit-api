<?php

namespace App\Domain\PlancherBas\Table;

use App\Domain\PlancherBas\Enum\Mitoyennete;

interface UeRepository
{
    public function search(int $id): UeCollection;
    public function search_by(Mitoyennete $mitoyennete, int $annee_construction): UeCollection;
}
