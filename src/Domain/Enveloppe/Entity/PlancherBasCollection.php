<?php

namespace App\Domain\Enveloppe\Entity;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Enveloppe\Enum\EtatIsolation;

/**
 * @property PlancherBas[] $elements
 */
final class PlancherBasCollection extends ParoiCollection
{
    public function reinitialise(): self
    {
        return $this->walk(fn(PlancherBas $item) => $item->reinitialise());
    }

    /** @inheritdoc */
    public function find(Id $id): ?PlancherBas
    {
        return parent::find($id);
    }

    /** @inheritdoc */
    public function with_isolation(EtatIsolation $isolation): static
    {
        return $this->filter(fn(PlancherBas $item) => $item->data()->isolation === $isolation);
    }
}
