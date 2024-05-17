<?php

namespace App\Domain\Ventilation;

use App\Domain\Common\Collection\ArrayCollection;
use App\Domain\Common\ValueObject\Id;

/**
 * @property InstallationVentilation[] $elements
 */
final class InstallationVentilationCollection extends ArrayCollection
{
    public function find(Id $id): ?InstallationVentilation
    {
        return $this->filter(fn (InstallationVentilation $item): bool => $item->id()->compare($id))->first();
    }
}
