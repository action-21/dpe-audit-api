<?php

namespace App\Domain\Ventilation;

use App\Domain\Common\ValueObject\Id;

interface VentilationRepository
{
    public function find(Id $audit_id): ?Ventilation;
}
