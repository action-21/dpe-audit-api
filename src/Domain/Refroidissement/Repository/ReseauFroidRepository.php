<?php

namespace App\Domain\Refroidissement\Repository;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Refroidissement\Entity\ReseauFroid;

interface ReseauFroidRepository
{
    public function find(Id $id): ?ReseauFroid;
}
