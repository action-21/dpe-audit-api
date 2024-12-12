<?php

namespace App\Domain\Simulation;

use App\Domain\Common\Type\Id;

interface SimulationRepository
{
    public function find(Id $audit_id): ?Simulation;
}
