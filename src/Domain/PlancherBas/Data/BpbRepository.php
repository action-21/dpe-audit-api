<?php

namespace App\Domain\PlancherBas\Data;

use App\Domain\PlancherBas\Enum\Mitoyennete;

interface BpbRepository
{
    public function find_by(Mitoyennete $mitoyennete): ?Bpb;
}
