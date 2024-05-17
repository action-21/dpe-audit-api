<?php

namespace App\Domain\MasqueLointain;

use App\Domain\Common\ValueObject\Id;

interface MasqueLointainRepository
{
    public function find(Id $id): ?MasqueLointain;
    public function search(Id $enveloppe_id): MasqueLointainCollection;
}
