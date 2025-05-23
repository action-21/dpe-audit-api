<?php

namespace App\Engine\Performance\Ventilation;

use App\Domain\Audit\Audit;
use App\Domain\Ventilation\Entity\{Installation, Systeme};
use App\Engine\Performance\Rule;

final class PerformanceInstallation extends Rule
{
    private Installation $installation;

    /**
     * Débit volumique conventionnel à reprendre exprimé en m3/(h.m²)
     * 
     * @see \App\Engine\Performance\Ventilation\PerformanceSysteme::qvarep_conv()
     * @see \App\Engine\Performance\Ventilation\DimensionnementSysteme::rdim()
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
     * @see \App\Engine\Performance\Ventilation\PerformanceSysteme::qvasouf_conv()
     * @see \App\Engine\Performance\Ventilation\DimensionnementSysteme::rdim()
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
     * @see \App\Engine\Performance\Ventilation\PerformanceSysteme::smea_conv()
     * @see \App\Engine\Performance\Ventilation\DimensionnementSysteme::rdim()
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
