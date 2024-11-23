<?php

namespace App\Domain\PlancherHaut\Data;

use App\Domain\PlancherHaut\Enum\Mitoyennete;

interface BphRepository
{
    public function find_by(Mitoyennete $mitoyennete): ?Bph;
}
