<?php

namespace App\Domain\Enveloppe;

use App\Domain\Common\ValueObject\Id;

interface EnveloppeRepository
{
    public function find(Id $audit_id): ?Enveloppe;
}
