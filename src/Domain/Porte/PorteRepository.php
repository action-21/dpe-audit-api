<?php

namespace App\Domain\Porte;

use App\Domain\Common\ValueObject\Id;

interface PorteRepository
{
    public function find(Id $id): ?Porte;
    public function search(Id $enveloppe_id): PorteCollection;
}
