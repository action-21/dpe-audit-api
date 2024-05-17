<?php

namespace App\Domain\Mur\Table;

use App\Domain\Mur\Enum\Mitoyennete;

interface BRepository
{
    public function find(int $id): ?B;
    public function find_by(Mitoyennete $mitoyennete): ?B;
}
