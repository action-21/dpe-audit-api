<?php

namespace App\Domain\Ecs;

use App\Domain\Ecs\Service\{MoteurBesoin, MoteurConsommation, MoteurDimensionnement, MoteurPerformance, MoteurPerte, MoteurRendement};
use App\Domain\Simulation\Simulation;

/**
 * @see App\Domain\Audit\AuditService::calcule()
 */
final class EcsService
{
    public function __construct(
        private MoteurBesoin $moteur_besoin,
        private MoteurDimensionnement $moteur_dimensionnement,
        private MoteurPerte $moteur_perte,
        private MoteurPerformance $moteur_performance,
        private MoteurRendement $moteur_rendement,
        private MoteurConsommation $moteur_consommation,
    ) {}

    public function calcule(Ecs $entity, Simulation $simulation): Ecs
    {
        $entity = $this->calcule_besoins($entity, $simulation);
        $entity = $this->calcule_performance($entity, $simulation);
        $entity = $this->calcule_dimensionnement($entity);
        $entity = $this->calcule_pertes($entity, $simulation);
        $entity = $this->calcule_rendement($entity);
        $entity = $this->calcule_consommations($entity);
        return $entity;
    }

    public function calcule_besoins(Ecs $entity, Simulation $simulation): Ecs
    {
        return $entity->calcule_besoins($this->moteur_besoin, $simulation);
    }

    public function calcule_dimensionnement(Ecs $entity): Ecs
    {
        return $entity->calcule_dimensionnement($this->moteur_dimensionnement);
    }

    public function calcule_performance(Ecs $entity, Simulation $simulation): Ecs
    {
        return $entity->calcule_performance($this->moteur_performance, $simulation);
    }

    public function calcule_pertes(Ecs $entity, Simulation $simulation): Ecs
    {
        return $entity->calcule_pertes($this->moteur_perte, $simulation);
    }

    public function calcule_rendement(Ecs $entity): Ecs
    {
        return $entity->calcule_rendement($this->moteur_rendement);
    }

    public function calcule_consommations(Ecs $entity): Ecs
    {
        return $entity->calcule_consommations($this->moteur_consommation);
    }
}
