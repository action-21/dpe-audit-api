<?php

namespace App\Domain\Enveloppe\Entity;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Enveloppe\Enum\EtatIsolation;

/**
 * @property PlancherHaut[] $elements
 */
final class PlancherHautCollection extends ParoiCollection
{
    public function reinitialise(): self
    {
        return $this->walk(fn(PlancherHaut $item) => $item->reinitialise());
    }

    /** @inheritdoc */
    public function find(Id $id): ?PlancherHaut
    {
        return parent::find($id);
    }

    /** @inheritdoc */
    public function with_isolation(EtatIsolation $isolation): static
    {
        return $this->filter(fn(PlancherHaut $item) => $item->data()->isolation === $isolation);
    }
}
