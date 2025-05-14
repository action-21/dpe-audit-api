<?php

namespace App\Domain\Refroidissement\Entity;

use App\Domain\Common\Collection\ArrayCollection;
use App\Domain\Common\ValueObject\Id;

/**
 * @extends ArrayCollection<Systeme>
 */
final class SystemeCollection extends ArrayCollection
{
    public function reinitialise(): self
    {
        return $this->walk(fn(Systeme $item) => $item->reinitialise());
    }

    public function with_installation(Id $id): self
    {
        return $this->filter(fn(Systeme $item) => $item->installation()->id()->compare($id));
    }

    public function with_generateur(Id $id): self
    {
        return $this->filter(fn(Systeme $item) => $item->generateur()->id()->compare($id));
    }
}
