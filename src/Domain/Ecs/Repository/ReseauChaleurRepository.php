<?php

namespace App\Domain\Ecs\Repository;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Ecs\Entity\ReseauChaleur;

interface ReseauChaleurRepository
{
    public function find(Id $id): ?ReseauChaleur;
}
