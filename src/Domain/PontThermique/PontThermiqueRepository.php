<?php

namespace App\Domain\PontThermique;

use App\Domain\Common\Type\Id;

interface PontThermiqueRepository
{
    public function find(Id $id): ?PontThermique;
    public function search(Id $enveloppe_id): PontThermiqueCollection;
}
