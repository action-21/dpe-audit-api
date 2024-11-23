<?php

namespace App\Domain\Eclairage;

use App\Domain\Common\Type\Id;

interface EclairageRepository
{
    public function find(Id $audit_id): ?Eclairage;
}
