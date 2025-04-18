<?php

namespace App\Domain\Enveloppe\Entity;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Enveloppe\Enum\EtatIsolation;

/**
 * @property Baie[] $elements
 */
final class BaieCollection extends ParoiCollection
{
    public function reinitialise(): self
    {
        return $this->walk(fn(Baie $item) => $item->reinitialise());
    }

    public function find(Id $id): ?Baie
    {
        return parent::find($id);
    }

    /** @inheritdoc */
    public function with_isolation(EtatIsolation $isolation): static
    {
        return $this->filter(fn(Baie $item) => $item->data()->isolation === $isolation);
    }
}
