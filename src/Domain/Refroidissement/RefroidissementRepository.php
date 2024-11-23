<?php

namespace App\Domain\Refroidissement;

use App\Domain\Common\Type\Id;

interface RefroidissementRepository
{
    public function find(Id $audit_id): ?Refroidissement;
}
