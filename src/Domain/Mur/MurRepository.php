<?php

namespace App\Domain\Mur;

use App\Domain\Common\ValueObject\Id;

interface MurRepository
{
    public function find(Id $id): ?Mur;
    public function search(Id $enveloppe_id): MurCollection;
}
