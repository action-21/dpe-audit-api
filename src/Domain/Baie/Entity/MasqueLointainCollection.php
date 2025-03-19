<?php

namespace App\Domain\Baie\Entity;

use App\Domain\Baie\Enum\SecteurChampsVision;
use App\Domain\Baie\Enum\TypeMasqueLointain;
use App\Domain\Common\Collection\ArrayCollection;
use App\Domain\Common\ValueObject\Id;

/**
 * @property MasqueLointain[] $elements
 */
final class MasqueLointainCollection extends ArrayCollection
{
    public function find(Id $id): ?MasqueLointain
    {
        return $this->findFirst(fn(mixed $key, MasqueLointain $item): bool => $item->id()->compare($id));
    }

    public function filter_by_type(TypeMasqueLointain $type_masque): self
    {
        return $this->filter(fn(MasqueLointain $item): bool => $item->type_masque() === $type_masque);
    }

    public function filter_by_secteur(SecteurChampsVision $secteur): self
    {
        return $this->filter(fn(MasqueLointain $item): bool => $item->secteur() === $secteur);
    }
}
