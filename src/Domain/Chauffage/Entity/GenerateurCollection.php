<?php

namespace App\Domain\Chauffage\Entity;

use App\Domain\Chauffage\Enum\CouvertureGeneration;
use App\Domain\Chauffage\Enum\UtilisationGenerateur;
use App\Domain\Common\Collection\ArrayCollection;
use App\Domain\Common\ValueObject\Id;

/**
 * @property Generateur[] $elements
 */
final class GenerateurCollection extends ArrayCollection
{
    public function find(Id $id): ?Generateur
    {
        return $this->filter(fn (Generateur $item) => $item->id()->compare($id))->first();
    }

    public function search_by_utilisation(UtilisationGenerateur $utilisation): self
    {
        return $this->filter(fn (Generateur $item) => $item->utilisation() === $utilisation);
    }

    public function effet_joule(): bool
    {
        return $this
            ->search_by_utilisation(UtilisationGenerateur::BASE)
            ->filter(fn (Generateur $item) => $item->type_generateur()->effet_joule())
            ->count() > 0;
    }
}
