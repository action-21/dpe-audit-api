<?php

namespace App\Domain\Enveloppe\Entity\Baie;

use App\Domain\Common\Collection\ArrayCollection;
use App\Domain\Common\ValueObject\Id;
use App\Domain\Enveloppe\Enum\Baie\TypeMasqueLointain;

/**
 * @extends ArrayCollection<MasqueLointain>
 */
final class MasqueLointainCollection extends ArrayCollection
{
    public function find(Id $id): ?MasqueLointain
    {
        return array_find(
            $this->elements,
            fn(MasqueLointain $item): bool => $item->id()->compare($id)
        );
    }

    public function with_type(TypeMasqueLointain $type_masque): static
    {
        return $this->filter(
            fn(MasqueLointain $item): bool => $item->type_masque() === $type_masque
        );
    }
}
