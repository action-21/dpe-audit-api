<?php

namespace App\Domain\Chauffage\Entity;

use App\Domain\Chauffage\Enum\{ConfigurationSysteme, TypeChauffage};
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

    public function find(Id $id): ?Systeme
    {
        return array_find(
            $this->elements,
            fn(Systeme $item): bool => $item->id()->compare($id),
        );
    }

    public function with_generateur_combustion(): static
    {
        return $this->filter(fn(Systeme $item): bool => null !== $item->generateur()->combustion());
    }

    public function with_systeme_collectif(): static
    {
        return $this->filter(fn(Systeme $item): bool => $item->generateur()->position()->generateur_collectif);
    }

    public function with_systeme_individuel(): static
    {
        return $this->filter(fn(Systeme $item): bool => false === $item->generateur()->position()->generateur_collectif);
    }

    public function with_type(TypeChauffage $type_chauffage): static
    {
        return $this->filter(fn(Systeme $item): bool => $item->type_chauffage() === $type_chauffage);
    }

    public function with_configuration(ConfigurationSysteme $configuration): static
    {
        return $this->filter(fn(Systeme $item): bool => $item->data()->configuration === $configuration);
    }

    public function with_generateur(Id $id): static
    {
        return $this->filter(fn(Systeme $item): bool => $item->generateur()->id()->compare($id));
    }

    public function with_emetteur(Id $id): static
    {
        return $this->filter(fn(Systeme $item): bool => $item->emetteurs()->find($id) !== null);
    }

    public function with_installation(Id $id): static
    {
        return $this->filter(fn(Systeme $item): bool => $item->installation()->id()->compare($id));
    }

    public function with_cascade(bool $cascade): self
    {
        return $cascade
            ? $this->filter(fn(Systeme $item): bool => $item->generateur()->signaletique()->priorite_cascade !== null)
            : $this->filter(fn(Systeme $item): bool => $item->generateur()->signaletique()->priorite_cascade === null);
    }

    public function with_priorite_cascade(bool $priorite): self
    {
        return $priorite
            ? $this->with_cascade(true)->filter(fn(Systeme $item): bool => $item->generateur()->signaletique()->priorite_cascade > 0)
            : $this->with_cascade(true)->filter(fn(Systeme $item): bool => $item->generateur()->signaletique()->priorite_cascade === 0);
    }

    public function has_generateur(Id $id): bool
    {
        return $this->with_generateur($id)->count() > 0;
    }

    public function has_generateur_collectif(): bool
    {
        return $this->with_systeme_collectif()->count() > 0;
    }

    public function has_systeme_central(): bool
    {
        return $this->with_type(TypeChauffage::CHAUFFAGE_CENTRAL)->count() > 0;
    }

    public function has_systeme_divise(): bool
    {
        return $this->with_type(TypeChauffage::CHAUFFAGE_DIVISE)->count() > 0;
    }

    public function has_systeme_central_collectif(): bool
    {
        return $this
            ->with_type(TypeChauffage::CHAUFFAGE_CENTRAL)
            ->with_systeme_collectif()
            ->count() > 0;
    }

    public function has_chaudiere(): bool
    {
        return $this->filter(fn(Systeme $item): bool => $item->generateur()->type()->is_chaudiere())->count() > 0;
    }

    public function has_chaudiere_bois(): bool
    {
        $collection = $this->filter(fn(Systeme $item): bool => $item->generateur()->energie()->is_bois());
        return $this->has_chaudiere() && $collection->count() > 0;
    }

    public function has_pac(): bool
    {
        return $this->filter(fn(Systeme $item): bool => $item->generateur()->type()->is_pac())->count() > 0;
    }

    public function has_cascade(): bool
    {
        return $this->with_cascade(true)->count() > 0;
    }

    public function effet_joule(): bool
    {
        if ($this->has_systeme_central()) {
            $collection = $this->with_type(TypeChauffage::CHAUFFAGE_CENTRAL);
            return $collection->filter(fn(Systeme $item): bool => $item->effet_joule())->count() > $collection->count() / 2;
        }
        return $this->filter(fn(Systeme $item): bool => $item->effet_joule())->count() > $this->count() / 2;
    }
}
