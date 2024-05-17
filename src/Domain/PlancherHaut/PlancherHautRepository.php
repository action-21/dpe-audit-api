<?php

namespace App\Domain\PlancherHaut;

use App\Domain\Common\ValueObject\Id;

interface PlancherHautRepository
{
    public function find(Id $id): ?PlancherHaut;
    public function search(Id $enveloppe_id): PlancherHautCollection;
}
