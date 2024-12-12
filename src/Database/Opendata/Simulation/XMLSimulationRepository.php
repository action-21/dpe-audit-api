<?php

namespace App\Database\Opendata\Simulation;

use App\Domain\Audit\AuditRepository;
use App\Domain\Chauffage\ChauffageRepository;
use App\Domain\Common\Type\Id;
use App\Domain\Eclairage\EclairageRepository;
use App\Domain\Ecs\EcsRepository;
use App\Domain\Enveloppe\EnveloppeRepository;
use App\Domain\Production\ProductionRepository;
use App\Domain\Refroidissement\RefroidissementRepository;
use App\Domain\Simulation\Simulation;
use App\Domain\Simulation\SimulationRepository;
use App\Domain\Ventilation\VentilationRepository;

final class XMLSimulationRepository implements SimulationRepository
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

    public function find(Id $audit_id): ?Simulation
    {
        if (null === $audit = $this->audit_repository->find($audit_id)) {
            return null;
        }
        if (null === $enveloppe = $this->enveloppe_repository->find($audit_id)) {
            return null;
        }
        if (null === $chauffage = $this->chauffage_repository->find($audit_id)) {
            return null;
        }
        if (null === $ecs = $this->ecs_repository->find($audit_id)) {
            return null;
        }
        if (null === $ventilation = $this->ventilation_repository->find($audit_id)) {
            return null;
        }
        if (null === $refroidissement = $this->refroidissement_repository->find($audit_id)) {
            return null;
        }
        if (null === $production = $this->production_repository->find($audit_id)) {
            return null;
        }
        if (null === $eclairage = $this->eclairage_repository->find($audit_id)) {
            return null;
        }
        return Simulation::create(
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
