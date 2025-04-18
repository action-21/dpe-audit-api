<?php

namespace App\Domain\Ventilation\Engine;

use App\Domain\Audit\Audit;
use App\Domain\Common\EngineRule;
use App\Domain\Ventilation\Entity\{Installation, Systeme};

final class PerformanceInstallation extends EngineRule
{
    private Installation $installation;

    /**
     * Débit volumique conventionnel à reprendre exprimé en m3/(h.m²)
     * 
     * @see \App\Domain\Ventilation\Engine\PerformanceSysteme::qvarep_conv()
     * @see \App\Domain\Ventilation\Engine\DimensionnementSysteme::rdim()
     */
    public function qvarep_conv(): float
    {
        return $this->installation->systemes()->reduce(
            fn(float $carry, Systeme $item): float => $carry + $item->data()->qvarep_conv * $item->data()->rdim,
        );
    }

    /**
     * Débit volumique conventionnel à souffler exprimé en m3/(h.m²)
     * 
     * @see \App\Domain\Ventilation\Engine\PerformanceSysteme::qvasouf_conv()
     * @see \App\Domain\Ventilation\Engine\DimensionnementSysteme::rdim()
     */
    public function qvasouf_conv(): float
    {
        return $this->installation->systemes()->reduce(
            fn(float $carry, Systeme $item): float => $carry + $item->data()->qvasouf_conv * $item->data()->rdim,
        );
    }

    /**
     * Somme des modules d’entrée d'air sous 20 Pa par unité de surface habitable exprimée en m3/(h.m²)
     * 
     * 
     * @see \App\Domain\Ventilation\Engine\PerformanceSysteme::smea_conv()
     * @see \App\Domain\Ventilation\Engine\DimensionnementSysteme::rdim()
     */
    public function smea_conv(): float
    {
        return $this->installation->systemes()->reduce(
            fn(float $carry, Systeme $item): float => $carry + $item->data()->smea_conv * $item->data()->rdim,
        );
    }

    public function apply(Audit $entity): void
    {
        foreach ($entity->ventilation()->installations() as $installation) {
            $this->installation = $installation;
            $installation->calcule($installation->data()->with(
                qvarep_conv: $this->qvarep_conv(),
                qvasouf_conv: $this->qvasouf_conv(),
                smea_conv: $this->smea_conv(),
            ));
        }
    }

    public static function dependencies(): array
    {
        return [
            PerformanceSysteme::class,
            DimensionnementSysteme::class,
        ];
    }
}
