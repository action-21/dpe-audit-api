<?php

namespace App\Domain\PlancherIntermediaire;

use App\Domain\Common\ValueObject\Id;

interface PlancherIntermediaireRepository
{
    public function find(Id $id): ?PlancherIntermediaire;
    public function search(Id $enveloppe_id): PlancherIntermediaireCollection;
}
