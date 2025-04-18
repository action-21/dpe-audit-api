<?php

namespace App\Domain\Ventilation\Engine;

use App\Domain\Audit\Audit;
use App\Domain\Common\EngineRule;
use App\Domain\Ventilation\Entity\Installation;
use App\Domain\Ventilation\Ventilation;

final class PerformanceVentilation extends EngineRule
{
    private Ventilation $ventilation;

    /**
     * Débit volumique conventionnel à reprendre exprimé en m3/(h.m²)
     * 
     * @see \App\Domain\Ventilation\Engine\PerformanceInstallation::qvarep_conv()
     * @see \App\Domain\Ventilation\Engine\DimensionnementInstallation::rdim()
     */
    public function qvarep_conv(): float
    {
        return $this->ventilation->installations()->reduce(
            fn(float $carry, Installation $item): float => $carry + $item->data()->qvarep_conv * $item->data()->rdim,
        );
    }

    /**
     * Débit volumique conventionnel à souffler exprimé en m3/(h.m²)
     * 
     * @see \App\Domain\Ventilation\Engine\PerformanceInstallation::qvasouf_conv()
     * @see \App\Domain\Ventilation\Engine\DimensionnementInstallation::rdim()
     */
    public function qvasouf_conv(): float
    {
        return $this->ventilation->installations()->reduce(
            fn(float $carry, Installation $item): float => $carry + $item->data()->qvasouf_conv * $item->data()->rdim,
        );
    }

    /**
     * Somme des modules d’entrée d'air sous 20 Pa par unité de surface habitable exprimée en m3/(h.m²)
     * 
     * @see \App\Domain\Ventilation\Engine\PerformanceInstallation::smea_conv()
     * @see \App\Domain\Ventilation\Engine\DimensionnementInstallation::rdim()
     */
    public function smea_conv(): float
    {
        return $this->ventilation->installations()->reduce(
            fn(float $carry, Installation $item): float => $carry + $item->data()->smea_conv * $item->data()->rdim,
        );
    }

    public function apply(Audit $entity): void
    {
        $this->ventilation = $entity->ventilation();
        $entity->ventilation()->calcule($entity->ventilation()->data()->with(
            qvarep_conv: $this->qvarep_conv(),
            qvasouf_conv: $this->qvasouf_conv(),
            smea_conv: $this->smea_conv(),
        ));
    }

    public static function dependencies(): array
    {
        return [
            PerformanceInstallation::class,
            DimensionnementInstallation::class,
        ];
    }
}
