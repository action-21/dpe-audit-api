<?php

namespace App\Domain\Audit;

use App\Domain\Audit\Audit;
use App\Domain\Audit\Service\{MoteurDimensionnement, MoteurScenarioConventionnel};
use App\Domain\Simulation\Simulation;

final class AuditService
{
    public function __construct(
        private MoteurDimensionnement $moteur_dimensionnement,
        private MoteurScenarioConventionnel $moteur_scenario_conventionnel,
    ) {}

    public function calcule_dimensionnement(Audit $entity): Audit
    {
        $entity->calcule_dimensionnement(moteur: $this->moteur_dimensionnement);
        return $entity;
    }

    public function calcule_occupation(Audit $entity): Audit
    {
        $entity->calcule_occupation(moteur: $this->moteur_scenario_conventionnel);
        return $entity;
    }

    public function calcule_situation(Audit $entity, Simulation $simulation): Audit
    {
        $entity->calcule_situation(moteur: $this->moteur_scenario_conventionnel, simulation: $simulation);
        return $entity;
    }
}
