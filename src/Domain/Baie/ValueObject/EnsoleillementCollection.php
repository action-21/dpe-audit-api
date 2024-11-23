<?php

namespace App\Domain\Baie\ValueObject;

use App\Domain\Common\Collection\ArrayCollection;
use App\Domain\Common\Enum\{Mois};

/**
 * @property Ensoleillement[] $elements
 */
final class EnsoleillementCollection extends ArrayCollection
{
    public function find(Mois $mois): Ensoleillement
    {
        foreach ($this->elements as $item) {
            if ($item->mois === $mois) {
                return $item;
            }
        }
    }

    public function sse(): float
    {
        return \array_reduce($this->elements, fn(float $sse, Ensoleillement $item) => $sse += $item->sse);
    }
}
