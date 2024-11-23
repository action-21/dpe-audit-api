<?php

namespace App\Domain\Mur\Data;

use App\Domain\Mur\Enum\Mitoyennete;

interface BmurRepository
{
    public function find_by(Mitoyennete $mitoyennete): ?Bmur;
}
