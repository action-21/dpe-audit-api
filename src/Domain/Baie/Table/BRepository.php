<?php

namespace App\Domain\Baie\Table;

use App\Domain\Baie\Enum\Mitoyennete;

interface BRepository
{
    public function find(int $id): ?B;
    public function find_by(Mitoyennete $mitoyennete): ?B;
}
