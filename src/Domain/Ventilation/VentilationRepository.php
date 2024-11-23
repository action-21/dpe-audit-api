<?php

namespace App\Domain\Ventilation;

use App\Domain\Common\Type\Id;

interface VentilationRepository
{
    public function find(Id $audit_id): ?Ventilation;
}
