<?php

namespace App\Domain\Paroi;

use App\Domain\Common\ValueObject\Id;

/**
 * @property ParoiOpaque[] $elements
 */
class ParoiOpaqueCollection extends ParoiCollection
{
    public function find(Id $id): ?ParoiOpaque
    {
        return parent::find($id);
    }

    public function search_paroi_lourde(): self
    {
        return $this->filter(fn (ParoiOpaque $item): bool => $item->paroi_lourde());
    }

    /**
     * Retourne une collection de façade
     */
    public function search_with_facade(): self
    {
        return $this->filter(fn (ParoiOpaque $item): bool => $item->facade());
    }
}
