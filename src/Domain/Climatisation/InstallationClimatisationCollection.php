<?php

namespace App\Domain\Climatisation;

use App\Domain\Common\Collection\ArrayCollection;
use App\Domain\Common\ValueObject\Id;

/**
 * @property InstallationClimatisation[] $elements
 */
final class InstallationClimatisationCollection extends ArrayCollection
{
    public function find(Id $id): ?InstallationClimatisation
    {
        return $this->filter(fn (InstallationClimatisation $item): bool => $item->id()->compare($id))->first();
    }
}
