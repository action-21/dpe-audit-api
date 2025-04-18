<?php

namespace App\Domain\Chauffage;

use App\Domain\Common\ValueObject\Id;

interface ChauffageRepository
{
    public function find(Id $id): ?Chauffage;
}
