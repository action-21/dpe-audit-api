<?php

namespace App\Domain\Chauffage;

use App\Domain\Common\Collection\ArrayCollection;

/**
 * @property InstallationChauffage[] $elements
 */
final class InstallationChauffageCollection extends ArrayCollection
{
    public function find(\Stringable $id): ?InstallationChauffage
    {
        return $this->filter(fn (InstallationChauffage $installation) => $installation->id() === $id)->first();
    }

    /**
     * Somme des surfaces chauffées par les installations de chauffage
     */
    public function surface(): float
    {
        return $this->reduce(fn (float $carry, InstallationChauffage $installation) => $carry + $installation->logement()->surface_habitable(), 0);
    }

    /**
     * Effet joule majoritaire
     */
    public function effet_joule(): bool
    {
        return $this->filter(fn (InstallationChauffage $item): bool => $item->effet_joule())->surface() > ($this->surface() / 2);
    }
}
