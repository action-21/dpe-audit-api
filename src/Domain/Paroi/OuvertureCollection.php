<?php

namespace App\Domain\Paroi;

use App\Domain\Common\ValueObject\Id;

/**
 * @property Ouverture[] $elements
 */
class OuvertureCollection extends ParoiCollection
{
    public function find(Id $id): ?Ouverture
    {
        return parent::find($id);
    }

    /**
     * Retourne une collection par paroi associée
     */
    public function search_by_paroi_opaque(ParoiOpaque $entity): self
    {
        return $this->filter(fn (Ouverture $item): bool => $item->paroi_opaque()?->id()->compare($entity->id()));
    }

    /**
     * Présence majoritaire de joint d'étanchéité
     */
    public function presence_joint(): bool
    {
        return $this->filter(fn (Ouverture $item): bool => $item->presence_joint())->surface_deperditive() > $this->surface_deperditive() / 2;
    }
}
