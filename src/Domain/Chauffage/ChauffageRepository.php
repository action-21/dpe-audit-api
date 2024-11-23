<?php

namespace App\Domain\Chauffage;

use App\Domain\Common\Type\Id;

interface ChauffageRepository
{
    public function find(Id $audit_id): ?Chauffage;
}
