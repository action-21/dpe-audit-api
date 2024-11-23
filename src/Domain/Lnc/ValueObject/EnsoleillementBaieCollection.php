<?php

namespace App\Domain\Lnc\ValueObject;

use App\Domain\Common\Collection\ArrayCollection;
use App\Domain\Common\Enum\{Mois};

/**
 * @property EnsoleillementBaie[] $elements
 */
final class EnsoleillementBaieCollection extends ArrayCollection
{
    public static function create(\Closure $callback): self
    {
        $collection = [];
        foreach (Mois::cases() as $mois) {
            $collection[] = $callback(mois: $mois);
        }
        return new self($collection);
    }

    public function find(Mois $mois): ?EnsoleillementBaie
    {
        foreach ($this->elements as $item) {
            if ($item->mois === $mois) {
                return $item;
            }
        }
        return null;
    }

    public function t(Mois $mois): ?float
    {
        return $this->find(mois: $mois)?->t;
    }

    public function sst(Mois $mois): ?float
    {
        return $this->find(mois: $mois)?->sst;
    }
}
