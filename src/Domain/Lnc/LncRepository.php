<?php

namespace App\Domain\Lnc;

use App\Domain\Common\Type\Id;

interface LncRepository
{
    public function find(Id $id): ?Lnc;
    public function search(Id $enveloppe_id): LncCollection;
}
