<?php

namespace App\Domain\Chauffage\Entity;

use App\Domain\Chauffage\Enum\{TypeChauffage};
use App\Domain\Chauffage\Service\{MoteurConsommation, MoteurDimensionnement, MoteurRendement};
use App\Domain\Chauffage\ValueObject\PerteCollection;
use App\Domain\Common\Collection\ArrayCollection;
use App\Domain\Common\Type\Id;
use App\Domain\Common\ValueObject\ConsommationCollection;
use App\Domain\Simulation\Simulation;

/**
 * @property Systeme[] $elements
 */
final class SystemeCollection extends ArrayCollection
{
    public function controle(): self
    {
        return $this->walk(fn(Systeme $item) => $item->controle());
    }

    public function reinitialise(): self
    {
        return $this->walk(fn(Systeme $item) => $item->reinitialise());
    }

    public function calcule_dimensionnement(MoteurDimensionnement $moteur): self
    {
        return $this->walk(fn(Systeme $item) => $item->calcule_dimensionnement($moteur));
    }

    public function calcule_rendement(MoteurRendement $moteur, Simulation $simulation): self
    {
        return $this->walk(fn(Systeme $item) => $item->calcule_rendement($moteur, $simulation));
    }

    public function calcule_consommations(MoteurConsommation $moteur, Simulation $simulation): self
    {
        return $this->walk(fn(Systeme $item) => $item->calcule_consommations($moteur, $simulation));
    }

    public function find(Id $id): ?Systeme
    {
        return $this->findFirst(fn(mixed $key, Systeme $item): bool => $item->id()->compare($id));
    }

    public function filter_by_generateur_combustion(): self
    {
        return $this->filter(fn(Systeme $item): bool => null !== $item->generateur()->combustion());
    }

    public function filter_by_systeme_collectif(): self
    {
        return $this->filter(fn(Systeme $item): bool => $item->generateur()->signaletique()->generateur_collectif);
    }

    public function filter_by_systeme_individuel(): self
    {
        return $this->filter(fn(Systeme $item): bool => false === $item->generateur()->signaletique()->generateur_collectif);
    }

    public function filter_by_type_chauffage(TypeChauffage $type_chauffage): self
    {
        return $this->filter(fn(Systeme $item): bool => $item->type_chauffage() === $type_chauffage);
    }

    public function filter_by_generateur(Id $id): self
    {
        return $this->filter(fn(Systeme $item): bool => $item->generateur()->id()->compare($id));
    }

    public function filter_by_cascade(bool $cascade): self
    {
        return $cascade
            ? $this->filter(fn(Systeme $item): bool => $item->generateur()->signaletique()->priorite_cascade !== null)
            : $this->filter(fn(Systeme $item): bool => $item->generateur()->signaletique()->priorite_cascade === null);
    }

    public function filter_by_priorite_cascade(bool $priorite): self
    {
        return $priorite
            ? $this->filter_by_cascade(true)->filter(fn(Systeme $item): bool => $item->generateur()->signaletique()->priorite_cascade > 0)
            : $this->filter_by_cascade(true)->filter(fn(Systeme $item): bool => $item->generateur()->signaletique()->priorite_cascade === 0);
    }

    public function has_generateur(Id $id): bool
    {
        return $this->exists(fn(mixed $key, Systeme $item): bool => $item->generateur()->id()->compare($id));
    }

    public function has_generateur_collectif(): bool
    {
        return $this->filter_by_systeme_collectif()->count() > 0;
    }

    public function has_systeme_central(): bool
    {
        return $this->filter_by_type_chauffage(TypeChauffage::CHAUFFAGE_CENTRAL)->count() > 0;
    }

    public function has_systeme_divise(): bool
    {
        return $this->filter_by_type_chauffage(TypeChauffage::CHAUFFAGE_DIVISE)->count() > 0;
    }

    public function has_chaudiere(): bool
    {
        return $this->filter(fn(Systeme $item): bool => $item->generateur()->type()->is_chaudiere())->count() > 0;
    }

    public function has_chaudiere_bois(): bool
    {
        return $this->has_chaudiere() && $this->filter(fn(Systeme $item): bool => $item->generateur()->energie()->is_bois())->count() > 0;
    }

    public function has_pac(): bool
    {
        return $this->filter(fn(Systeme $item): bool => $item->generateur()->type()->is_pac())->count() > 0;
    }

    public function has_cascade(): bool
    {
        return $this->filter_by_cascade(true)->count() > 0;
    }

    public function has_pn(): bool
    {
        foreach ($this->elements as $item) {
            if (null === $item->generateur()->signaletique()?->pn)
                return false;
        }
        return true;
    }

    public function effet_joule(): bool
    {
        if ($this->has_systeme_central()) {
            $collection = $this->filter_by_type_chauffage(TypeChauffage::CHAUFFAGE_CENTRAL);
            return $collection->filter(fn(Systeme $item): bool => $item->effet_joule())->count() > $collection->count() / 2;
        }
        return $this->filter(fn(Systeme $item): bool => $item->effet_joule())->count() > $this->count() / 2;
    }

    public function pn(): float
    {
        return $this->reduce(fn(float $carry, Systeme $item): float => $carry += $item->pn() ?? 0);
    }

    public function pertes(): PerteCollection
    {
        return $this->reduce(fn(PerteCollection $collection, Installation $item): PerteCollection => $collection->merge(
            $item->systemes()->pertes(),
        ), new PerteCollection());
    }

    public function consommations(): ConsommationCollection
    {
        return $this->reduce(fn(ConsommationCollection $collection, Systeme $item): ConsommationCollection => $collection->merge(
            $item->consommations(),
            $item->consommations_auxiliaires(),
        ), new ConsommationCollection());
    }
}
