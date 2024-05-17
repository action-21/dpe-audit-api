<?php

namespace App\Domain\Batiment;

use App\Domain\Common\ValueObject\Id;

interface BatimentRepository
{
    public function find(Id $id, bool $eager = false): ?Batiment;
}
