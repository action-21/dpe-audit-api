<?php

namespace App\Domain\Simulation;

use App\Domain\Audit\AuditRepository;
use App\Domain\Chauffage\ChauffageRepository;
use App\Domain\Common\Type\Id;
use App\Domain\Eclairage\EclairageRepository;
use App\Domain\Ecs\EcsRepository;
use App\Domain\Enveloppe\EnveloppeRepository;
use App\Domain\Production\ProductionRepository;
use App\Domain\Refroidissement\RefroidissementRepository;
use App\Domain\Ventilation\VentilationRepository;

final class SimulationFactory
{
    public function __construct(
        private AuditRepository $audit_repository,
        private ChauffageRepository $chauffage_repository,
        private EclairageRepository $eclairage_repository,
        private EcsRepository $ecs_repository,
        private EnveloppeRepository $enveloppe_repository,
        private ProductionRepository $production_repository,
        private RefroidissementRepository $refroidissement_repository,
        private VentilationRepository $ventilation_repository,
    ) {}

    public function from_audit(Id $id): Simulation
    {
        $audit = $this->audit_repository->find($id);
        $enveloppe = $this->enveloppe_repository->find($id);
        $chauffage = $this->chauffage_repository->find($id);
        $ecs = $this->ecs_repository->find($id);
        $ventilation = $this->ventilation_repository->find($id);
        $refroidissement = $this->refroidissement_repository->find($id);
        $production = $this->production_repository->find($id);
        $eclairage = $this->eclairage_repository->find($id);

        return new Simulation(
            id: Id::create(),
            audit: $audit,
            enveloppe: $enveloppe,
            chauffage: $chauffage,
            ecs: $ecs,
            ventilation: $ventilation,
            refroidissement: $refroidissement,
            production: $production,
            eclairage: $eclairage,
        );
    }
}
