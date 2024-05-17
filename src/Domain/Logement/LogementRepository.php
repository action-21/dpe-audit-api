<?php

namespace App\Domain\Logement;

use App\Domain\Common\ValueObject\Id;

interface LogementRepository
{
    public function find(Id $id): ?Logement;
    public function search(Id $batiment_id): LogementCollection;
}
