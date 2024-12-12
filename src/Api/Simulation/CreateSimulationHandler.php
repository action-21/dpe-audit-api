<?php

namespace App\Api\Simulation;

use App\Api\Audit\CreateAuditHandler;
use App\Api\Chauffage\CreateChauffageHandler;
use App\Api\Ecs\CreateEcsHandler;
use App\Api\Enveloppe\CreateEnveloppeHandler;
use App\Api\Production\CreateProductionHandler;
use App\Api\Refroidissement\CreateRefroidissementHandler;
use App\Api\Simulation\Payload\SimulationPayload;
use App\Api\Ventilation\CreateVentilationHandler;
use App\Domain\Eclairage\Eclairage;
use App\Domain\Simulation\{Simulation, SimulationService};

final class CreateSimulationHandler
{
    public function __construct(
        private CreateAuditHandler $create_audit_handler,
        private CreateEnveloppeHandler $create_enveloppe_handler,
        private CreateVentilationHandler $create_ventilation_handler,
        private CreateChauffageHandler $create_chauffage_handler,
        private CreateEcsHandler $create_ecs_handler,
        private CreateRefroidissementHandler $create_refroidissement_handler,
        private CreateProductionHandler $create_production_handler,
        private SimulationService $simulation_service,
    ) {}

    public function __invoke(SimulationPayload $payload): Simulation
    {
        $audit = ($this->create_audit_handler)($payload->audit);
        $enveloppe = ($this->create_enveloppe_handler)($payload->enveloppe, $audit);
        $ventilation = ($this->create_ventilation_handler)($payload->ventilation, $audit);
        $chauffage = ($this->create_chauffage_handler)($payload->chauffage, $audit);
        $ecs = ($this->create_ecs_handler)($payload->ecs, $audit);
        $refroidissement = ($this->create_refroidissement_handler)($payload->refroidissement, $audit);
        $production = ($this->create_production_handler)($payload->production, $audit);

        $simulation = Simulation::create(
            audit: $audit,
            enveloppe: $enveloppe,
            chauffage: $chauffage,
            ecs: $ecs,
            ventilation: $ventilation,
            refroidissement: $refroidissement,
            production: $production,
            eclairage: Eclairage::create(audit: $audit),
        );

        return $this->simulation_service->calcule($simulation);
    }
}
