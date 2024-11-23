<?php

namespace App\Domain\Refroidissement;

use App\Domain\Refroidissement\Service\{MoteurBesoin, MoteurConsommation, MoteurDimensionnement, MoteurPerformance};
use App\Domain\Simulation\Simulation;

/**
 * @see App\Domain\Audit\AuditService::calcule()
 */
final class RefroidissementService
{
    public function __construct(
        private MoteurBesoin $moteur_besoin,
        private MoteurDimensionnement $moteur_dimensionnement,
        private MoteurPerformance $moteur_performance,
        private MoteurConsommation $moteur_consommation,
    ) {}

    public function calcule(Refroidissement $entity, Simulation $simulation): Refroidissement
    {
        $entity->calcule_besoins($this->moteur_besoin, $simulation);
        $entity->calcule_performance($this->moteur_performance);
        $entity->calcule_dimensionnement($this->moteur_dimensionnement);
        $entity->calcule_consommations($this->moteur_consommation);
        return $entity;
    }

    public function calcule_besoins(Refroidissement $entity, Simulation $simulation): Refroidissement
    {
        $entity->calcule_besoins($this->moteur_besoin, $simulation);
        return $entity;
    }

    public function calcule_dimensionnement(Refroidissement $entity): Refroidissement
    {
        $entity->calcule_dimensionnement($this->moteur_dimensionnement);
        return $entity;
    }

    public function calcule_performance(Refroidissement $entity): Refroidissement
    {
        $entity->calcule_performance($this->moteur_performance);
        return $entity;
    }

    public function calcule_consommations(Refroidissement $entity): Refroidissement
    {
        $entity->calcule_consommations($this->moteur_consommation);
        return $entity;
    }
}
