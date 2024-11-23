<?php

namespace App\Domain\Baie;

use App\Domain\Common\Type\Id;

interface BaieRepository
{
    public function find(Id $id): ?Baie;
    public function search(Id $enveloppe_id): BaieCollection;
}
