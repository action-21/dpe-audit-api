<?php

namespace App\Domain\Simulation;

use App\Domain\Common\ValueObject\Id;

interface SimulationRepository
{
    public function find(Id $audit_id): ?Simulation;
}
