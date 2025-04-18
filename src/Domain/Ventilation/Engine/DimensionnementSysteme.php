<?php

namespace App\Domain\Ventilation\Engine;

use App\Domain\Audit\Audit;
use App\Domain\Common\EngineRule;
use App\Domain\Ventilation\Entity\Systeme;

final class DimensionnementSysteme extends EngineRule
{
    private Systeme $systeme;

    /**
     * Ratio de dimensionnement du système de ventilation
     */
    public function rdim(): float
    {
        $n = $this->systeme->ventilation()->systemes()
            ->with_installation($this->systeme->installation()->id())
            ->count();

        return 1 / $n;
    }

    public function apply(Audit $entity): void
    {
        foreach ($entity->ventilation()->systemes() as $systeme) {
            $this->systeme = $systeme;
            $systeme->calcule($systeme->data()->with(
                rdim: $this->rdim()
            ));
        }
    }
}
