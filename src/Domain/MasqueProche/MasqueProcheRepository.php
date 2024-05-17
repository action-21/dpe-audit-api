<?php

namespace App\Domain\MasqueProche;

use App\Domain\Common\ValueObject\Id;

interface MasqueProcheRepository
{
    public function find(Id $id): ?MasqueProche;
    public function search(Id $baie_id): MasqueProcheCollection;
}
