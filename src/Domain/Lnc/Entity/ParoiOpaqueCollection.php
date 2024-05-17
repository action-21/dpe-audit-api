<?php

namespace App\Domain\Lnc\Entity;

use App\Domain\Common\ValueObject\Id;

/**
 * @property ParoiOpaque[] $elements
 */
final class ParoiOpaqueCollection extends ParoiCollection
{
    public function find(Id $id): ?ParoiOpaque
    {
        return $this->findFirst(fn (mixed $key, ParoiOpaque $item): bool => $item->id()->compare($id));
    }
}
