<?php

namespace App\Domain\Refroidissement;

use App\Domain\Common\ValueObject\Id;

interface RefroidissementRepository
{
    public function find(Id $id): ?Refroidissement;
}
