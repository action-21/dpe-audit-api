<?php

namespace App\Domain\Lnc\Entity;

use App\Domain\Common\Enum\{Mois, Orientation};
use App\Domain\Common\ValueObject\Id;
use App\Domain\Lnc\Enum\Mitoyennete;
use App\Domain\Lnc\Service\{MoteurEnsoleillementBaie, MoteurSurfaceDeperditiveBaie};

/**
 * @property Baie[] $elements
 */
final class BaieCollection extends ParoiCollection
{
    public function controle(): void
    {
        $this->walk(fn(Baie $item) => $item->controle());
    }

    public function reinitialise(): static
    {
        return $this->walk(fn(Baie $item) => $item->reinitialise());
    }

    public function calcule_surface_deperditive(MoteurSurfaceDeperditiveBaie $moteur): self
    {
        return $this->walk(fn(Baie $entity) => $entity->surface_deperditive($moteur));
    }

    public function calcule_ensoleillement(MoteurEnsoleillementBaie $moteur): self
    {
        return $this->walk(fn(Baie $entity) => $entity->calcule_ensoleillement($moteur));
    }

    public function find(Id $id): ?Baie
    {
        return $this->findFirst(fn(mixed $key, Baie $item): bool => $item->id()->compare($id));
    }

    public function filter_by_paroi(Id $id): self
    {
        return $this->filter(fn(Baie $item): bool => $item->paroi()?->id()->compare($id) ?? false);
    }

    public function filter_by_mitoyennete(Mitoyennete $mitoyennete): self
    {
        return $this->filter(fn(Baie $item): bool => $item->position()->mitoyennete === $mitoyennete);
    }

    public function filter_by_orientation(Orientation $orientation): self
    {
        return $this->filter(fn(Baie $item): bool => $item->position()->orientation && Orientation::from_azimut($item->position()->orientation) === $orientation);
    }

    public function filter_by_inclinaison(bool $est_verticale): self
    {
        return $this->filter(fn(Baie $item): bool => $item->position()->inclinaison >= 75 === $est_verticale);
    }

    /** @return Orientation[] */
    public function orientations(): array
    {
        $orientations = [];
        $surface_principale = 0;

        foreach (Orientation::cases() as $orientation) {
            $surface = $this->filter_by_orientation($orientation)->surface();
            if ($surface > $surface_principale) {
                $orientations = [$orientation];
                $surface_principale = $surface;
            }
            if ($surface === $surface_principale) {
                $orientations[] = $orientation;
                $surface_principale = $surface;
            }
        }
        return $orientations;
    }

    public function surface(): float
    {
        return $this->reduce(fn(float $carry, Baie $item): float => $carry += $item->position()->surface);
    }

    public function t(Mois $mois): float
    {
        $surface = $this->surface();
        return $this->reduce(fn(float $carry, Baie $item): float => $carry += ($item->ensoleillement()?->t($mois) ?? 0) * ($item->position()->surface / $surface));
    }

    public function sst(Mois $mois): float
    {
        return $this->reduce(fn(float $carry, Baie $item): float => $carry += $item->ensoleillement()?->sst($mois) ?? 0);
    }
}
