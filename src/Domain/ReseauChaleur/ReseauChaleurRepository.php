<?php

namespace App\Domain\ReseauChaleur;

use App\Domain\Common\ValueObject\Id;

interface ReseauChaleurRepository
{
    public function find(Id $id): ?ReseauChaleur;
}
