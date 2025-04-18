<?php

namespace App\Domain\Chauffage\Repository;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Chauffage\Entity\ReseauChaleur;

interface ReseauChaleurRepository
{
    public function find(Id $id): ?ReseauChaleur;
}
