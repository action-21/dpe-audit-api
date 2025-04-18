<?php

namespace App\Domain\Eclairage;

use App\Domain\Common\ValueObject\Id;

interface EclairageRepository
{
    public function find(Id $id): ?Eclairage;
}
