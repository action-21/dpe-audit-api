<?php

namespace App\Domain\Ventilation;

use App\Domain\Simulation\Simulation;
use App\Domain\Ventilation\Service\{MoteurConsommation, MoteurDimensionnement, MoteurPerformance};

/**
 * @see App\Domain\Audit\AuditService::calcule()
 */
final class VentilationService
{
    public function __construct(
        private MoteurDimensionnement $moteur_dimensionnement,
        private MoteurPerformance $moteur_performance,
        private MoteurConsommation $moteur_consommation,
    ) {}

    public function calcule(Ventilation $entity): Ventilation
    {
        $entity = $this->calcule_performance($entity);
        $entity = $this->calcule_dimensionnement($entity);
        $entity = $this->calcule_consommations($entity);
        return $entity;
    }

    public function calcule_dimensionnement(Ventilation $entity): Ventilation
    {
        $entity->calcule_dimensionnement(moteur: $this->moteur_dimensionnement);
        return $entity;
    }

    public function calcule_performance(Ventilation $entity): Ventilation
    {
        $entity->calcule_performance(moteur: $this->moteur_performance);
        return $entity;
    }

    public function calcule_consommations(Ventilation $entity): Ventilation
    {
        $entity->calcule_consommations(moteur: $this->moteur_consommation);
        return $entity;
    }

    public function smea_conv(Ventilation $entity): float
    {
        return $entity->installations()->smea_conv();
    }

    public function qvarep_conv(Ventilation $entity): float
    {
        return $entity->installations()->qvarep_conv();
    }

    public function qvasouf_conv(Ventilation $entity): float
    {
        return $entity->installations()->qvasouf_conv();
    }
}
