<?php

namespace App\Engine\Performance\Refroidissement;

use App\Domain\Audit\Audit;
use App\Domain\Refroidissement\Entity\Systeme;
use App\Engine\Performance\Rule;

final class DimensionnementSysteme extends Rule
{
    private Systeme $systeme;

    /**
     * Ratio de dimensionnement du système de refroidissement
     */
    public function rdim(): float
    {
        $n = $this->systeme->refroidissement()->systemes()
            ->with_installation($this->systeme->installation()->id())
            ->count();

        return 1 / $n;
    }

    public function apply(Audit $entity): void
    {
        foreach ($entity->refroidissement()->systemes() as $systeme) {
            $this->systeme = $systeme;
            $systeme->calcule($systeme->data()->with(
                rdim: $this->rdim()
            ));
        }
    }
}
