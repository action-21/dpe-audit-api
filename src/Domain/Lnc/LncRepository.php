<?php

namespace App\Domain\Lnc;

use App\Domain\Common\ValueObject\Id;

interface LncRepository
{
    public function find(Id $id): ?Lnc;
    public function search(Id $enveloppe_id): LncCollection;
}
