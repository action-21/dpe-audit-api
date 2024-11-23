<?php

namespace App\Domain\Baie\Data;

use App\Domain\Baie\Enum\Mitoyennete;

interface BbaieRepository
{
    public function find_by(Mitoyennete $mitoyennete): ?Bbaie;
}
