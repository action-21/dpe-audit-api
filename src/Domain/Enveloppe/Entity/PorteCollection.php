<?php

namespace App\Domain\Enveloppe\Entity;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Enveloppe\Enum\EtatIsolation;

/**
 * @property Porte[] $elements
 */
final class PorteCollection extends ParoiCollection
{
    public function reinitialise(): self
    {
        return $this->walk(fn(Porte $item) => $item->reinitialise());
    }

    /** @inheritdoc */
    public function find(Id $id): ?Porte
    {
        return parent::find($id);
    }

    /** @inheritdoc */
    public function with_isolation(EtatIsolation $isolation): static
    {
        return $this->filter(fn(Porte $item) => $item->data()->isolation === $isolation);
    }
}
