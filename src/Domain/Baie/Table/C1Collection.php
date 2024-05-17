<?php

namespace App\Domain\Baie\Table;

use App\Domain\Common\Enum\Mois;
use App\Domain\Common\Table\TableValueCollection;

/**
 * @property C1[] $elements
 */
class C1Collection extends TableValueCollection
{
    public function find(Mois $mois): ?C1
    {
        return $this->filter(fn (C1 $item): bool => $item->mois() === $mois)->first();
    }
}
