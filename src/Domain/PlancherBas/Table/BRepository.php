<?php

namespace App\Domain\PlancherBas\Table;

use App\Domain\PlancherBas\Enum\Mitoyennete;

interface BRepository
{
    public function find(int $id): ?B;
    public function find_by(Mitoyennete $mitoyennete): ?B;
}
