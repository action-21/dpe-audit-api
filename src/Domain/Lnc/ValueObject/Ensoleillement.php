<?php

namespace App\Domain\Lnc\ValueObject;

use App\Domain\Common\Collection\ArrayCollection;
use App\Domain\Common\Enum\{Mois};

/**
 * @property EnsoleillementItem[] $elements
 */
final class Ensoleillement extends ArrayCollection
{
    public static function create(\Closure $callback): self
    {
        $collection = [];
        foreach (Mois::cases() as $mois) {
            $collection[] = $callback(mois: $mois);
        }
        return new self($collection);
    }

    public function find(Mois $mois): ?EnsoleillementItem
    {
        foreach ($this->elements as $item) {
            if ($item->mois === $mois) {
                return $item;
            }
        }
        return null;
    }
}
