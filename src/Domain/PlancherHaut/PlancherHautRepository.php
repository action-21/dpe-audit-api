<?php

namespace App\Domain\PlancherHaut;

use App\Domain\Common\Type\Id;

interface PlancherHautRepository
{
    public function find(Id $id): ?PlancherHaut;
    public function search(Id $enveloppe_id): PlancherHautCollection;
}
