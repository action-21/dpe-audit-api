<?php

namespace App\Domain\Refend;

use App\Domain\Common\ValueObject\Id;

interface RefendRepository
{
    public function find(Id $id): ?Refend;
    public function search(Id $enveloppe_id): RefendCollection;
}
