<?php

namespace App\Domain\Document;

use App\Domain\Common\Collection\ArrayCollection;

/**
 * @property Document[] $elements
 */
final class DocumentCollection extends ArrayCollection
{
    public function find(\Stringable $id): ?Document
    {
        return $this->findFirst(fn (mixed $key, Document $item): bool => $item->id()->compare($id));
    }
}
