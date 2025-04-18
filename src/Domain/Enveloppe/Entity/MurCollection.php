<?php

namespace App\Domain\Enveloppe\Entity;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Enveloppe\Entity\ParoiCollection;
use App\Domain\Enveloppe\Enum\EtatIsolation;

/**
 * @property Mur[] $elements
 */
final class MurCollection extends ParoiCollection
{
    public function reinitialise(): self
    {
        return $this->walk(fn(Mur $item) => $item->reinitialise());
    }

    /** @inheritdoc */
    public function find(Id $id): ?Mur
    {
        return parent::find($id);
    }

    /** @inheritdoc */
    public function with_isolation(EtatIsolation $isolation): static
    {
        return $this->filter(fn(Mur $item) => $item->data()->isolation === $isolation);
    }

    public function with_paroi_ancienne(bool $paroi_ancienne): static
    {
        return $this->filter(
            fn(Mur $item) => $item->paroi_ancienne() === $paroi_ancienne,
        );
    }
}
