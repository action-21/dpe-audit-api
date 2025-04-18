<?php

namespace App\Domain\Ecs\Engine\Dimensionnement;

use App\Domain\Audit\Audit;
use App\Domain\Common\EngineRule;
use App\Domain\Ecs\Entity\Systeme;

final class DimensionnementSysteme extends EngineRule
{
    private Systeme $systeme;

    /**
     * Ratio de dimensionnement du système d'eau chaude sanitaire
     */
    public function rdim(): float
    {
        $n = $this->systeme->ecs()->systemes()
            ->with_installation($this->systeme->installation()->id())
            ->count();
        return 1 / $n;
    }

    public function apply(Audit $entity): void
    {
        foreach ($entity->ecs()->systemes() as $systeme) {
            $this->systeme = $systeme;
            $systeme->calcule($systeme->data()->with(
                rdim: $this->rdim()
            ));
        }
    }
}
