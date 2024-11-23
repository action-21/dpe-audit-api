<?php

namespace App\Domain\PlancherBas;

use App\Domain\Common\Type\Id;

interface PlancherBasRepository
{
    public function find(Id $id): ?PlancherBas;
    public function search(Id $enveloppe_id): PlancherBasCollection;
}
