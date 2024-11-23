<?php

namespace App\Domain\ReseauChaleur;

use App\Domain\Common\Type\Id;

interface ReseauChaleurRepository
{
    public function find(Id $id): ?ReseauChaleur;
}
