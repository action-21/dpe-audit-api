<?php

namespace App\Domain\Ecs\ValueObject;

use App\Domain\Common\Collection\ArrayCollection;
use App\Domain\Common\Enum\{ScenarioUsage};

/**
 * @property Rendement[] $elements
 */
final class RendementCollection extends ArrayCollection
{
    public function find(ScenarioUsage $scenario): ?Rendement
    {
        foreach ($this->elements as $item) {
            if ($item->scenario === $scenario)
                return $item;
        }
        return null;
    }

    public function iecs(ScenarioUsage $scenario): ?float
    {
        return $this->find(scenario: $scenario)?->iecs;
    }

    public function fecs(ScenarioUsage $scenario): ?float
    {
        return $this->find(scenario: $scenario)?->fecs;
    }
}
