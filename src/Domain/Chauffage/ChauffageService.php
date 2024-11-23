<?php

namespace App\Domain\Chauffage;

use App\Domain\Chauffage\Service\{MoteurBesoin, MoteurConsommation, MoteurDimensionnement, MoteurPerformance, MoteurPerte, MoteurRendement};
use App\Domain\Simulation\Simulation;

/**
 * @see App\Domain\Audit\AuditService::calcule()
 */
final class ChauffageService
{
    public function __construct(
        private MoteurBesoin $moteur_besoin,
        private MoteurDimensionnement $moteur_dimensionnement,
        private MoteurPerformance $moteur_performance,
        private MoteurPerte $moteur_perte,
        private MoteurRendement $moteur_rendement,
        private MoteurConsommation $moteur_consommation,
    ) {}

    public function calcule(Chauffage $entity, Simulation $simulation): Chauffage
    {
        $entity = $this->calcule_performance($entity, $simulation);
        $entity = $this->calcule_pertes($entity, $simulation);
        $entity = $this->calcule_besoins($entity, $simulation);
        $entity = $this->calcule_rendement($entity, $simulation);
        $entity = $this->calcule_dimensionnement($entity, $simulation);
        $entity = $this->calcule_consommations($entity, $simulation);
        return $entity;
    }

    public function calcule_dimensionnement(Chauffage $entity, Simulation $simulation): Chauffage
    {
        return $entity->calcule_dimensionnement($this->moteur_dimensionnement, $simulation);
    }

    public function calcule_pertes(Chauffage $entity, Simulation $simulation): Chauffage
    {
        return $entity->calcule_pertes($this->moteur_perte, $simulation);
    }

    public function calcule_besoins(Chauffage $entity, Simulation $simulation): Chauffage
    {
        return $entity->calcule_besoins($this->moteur_besoin, $simulation);
    }

    public function calcule_performance(Chauffage $entity, Simulation $simulation): Chauffage
    {
        return $entity->calcule_performance($this->moteur_performance, $simulation);
    }

    public function calcule_rendement(Chauffage $entity, Simulation $simulation): Chauffage
    {
        return $entity->calcule_rendement($this->moteur_rendement, $simulation);
    }

    public function calcule_consommations(Chauffage $entity, Simulation $simulation): Chauffage
    {
        return $entity->calcule_consommations($this->moteur_consommation, $simulation);
    }
}
