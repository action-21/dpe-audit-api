<?php

namespace App\Domain\Simulation;

use App\Domain\Audit\AuditService;
use App\Domain\Chauffage\ChauffageService;
use App\Domain\Eclairage\EclairageService;
use App\Domain\Ecs\EcsService;
use App\Domain\Enveloppe\EnveloppeService;
use App\Domain\Production\ProductionService;
use App\Domain\Refroidissement\RefroidissementService;
use App\Domain\Simulation\Service\MoteurPerformance;
use App\Domain\Ventilation\VentilationService;

final class SimulationService
{
    public function __construct(
        private AuditService $audit_service,
        private ChauffageService $chauffage_service,
        private EcsService $ecs_service,
        private EnveloppeService $enveloppe_service,
        private ProductionService $production_service,
        private RefroidissementService $refroidissement_service,
        private VentilationService $ventilation_service,
        private EclairageService $eclairage_service,
        private MoteurPerformance $moteur_performance,
    ) {}

    public function calcule(Simulation $entity): Simulation
    {
        // 1. État d'inertie de l'enveloppe
        $this->enveloppe_service->calcule_surface_deperditive($entity->enveloppe());
        $this->enveloppe_service->calcule_inertie($entity->enveloppe());
        // 2. Approche conventionnelle
        $this->audit_service->calcule_occupation($entity->audit());
        $this->audit_service->calcule_situation($entity->audit(), $entity);
        // TODO: à garder ?
        $this->audit_service->calcule_dimensionnement($entity->audit());
        // 3. Ventilation
        $this->ventilation_service->calcule($entity->ventilation());
        // 4. Enveloppe
        $this->enveloppe_service->calcule_performance($entity->enveloppe(), $entity);
        $this->enveloppe_service->calcule_apport($entity->enveloppe(), $entity);
        // 5. Eau chaude sanitaire
        $this->ecs_service->calcule($entity->ecs(), $entity);
        // 6. Chauffage
        $this->chauffage_service->calcule($entity->chauffage(), $entity);
        // 7. Refroidissement
        $this->refroidissement_service->calcule($entity->refroidissement(), $entity);
        // 8. Eclairage
        $this->eclairage_service->calcule($entity->eclairage());
        // 9. Production
        $this->production_service->calcule_production($entity->production(), $entity);
        // 10. Performances
        $entity->calcule_performance($this->moteur_performance);

        return $entity;
    }
}
