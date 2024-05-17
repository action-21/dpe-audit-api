<?php

namespace App\Domain\Ecs;

use App\Domain\Common\Collection\ArrayCollection;
use App\Domain\Common\ValueObject\Id;

/**
 * @property InstallationEcs[] $elements
 */
final class InstallationEcsCollection extends ArrayCollection
{
    public function find(Id $id): ?InstallationEcs
    {
        return $this->filter(fn (InstallationEcs $item) => $item->id()->compare($id))->first();
    }

    /**
     * Somme des surfaces de références des installations d'ECS en m²
     */
    public function surface_reference(): float
    {
        return $this->reduce(fn (float $carry, InstallationEcs $installation) => $carry += $installation->surface_reference(), 0);
    }
}
