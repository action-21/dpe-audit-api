<?php

namespace App\Domain\PlancherBas;

use App\Domain\Common\ValueObject\Id;

interface PlancherBasRepository
{
    public function find(Id $id): ?PlancherBas;
    public function search(Id $id_enveloppe): PlancherBasCollection;
}
