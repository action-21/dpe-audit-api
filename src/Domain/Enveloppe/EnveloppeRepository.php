<?php

namespace App\Domain\Enveloppe;

use App\Domain\Common\Type\Id;

interface EnveloppeRepository
{
    public function find(Id $audit_id): ?Enveloppe;
}
