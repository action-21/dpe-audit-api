<?php

namespace App\Domain\Porte\Table;

use App\Domain\Porte\Enum\Mitoyennete;

interface BRepository
{
    public function find(int $id): ?B;
    public function find_by(Mitoyennete $mitoyennete): ?B;
}
