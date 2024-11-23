<?php

namespace App\Domain\Lnc\ValueObject;

use App\Domain\Common\Collection\ArrayCollection;
use App\Domain\Common\Enum\{Mois};

/**
 * @property Ensoleillement[] $elements
 */
final class EnsoleillementCollection extends ArrayCollection
{
    public static function create(\Closure $callback): self
    {
        $collection = [];
        foreach (Mois::cases() as $mois) {
            $collection[] = $callback(mois: $mois);
        }
        return new self($collection);
    }

    public function find(Mois $mois): ?Ensoleillement
    {
        foreach ($this->elements as $item) {
            if ($item->mois === $mois) {
                return $item;
            }
        }
        return null;
    }
}
