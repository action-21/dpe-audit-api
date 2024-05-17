<?php

namespace App\Domain\PlancherHaut\Table;

use App\Domain\PlancherHaut\Enum\Mitoyennete;

interface BRepository
{
    public function find(int $id): ?B;
    public function find_by(Mitoyennete $mitoyennete): ?B;
}
