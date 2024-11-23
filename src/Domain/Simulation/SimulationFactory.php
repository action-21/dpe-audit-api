<?php

namespace App\Domain\Simulation;

use App\Domain\Audit\{Audit, AuditRepository};
use App\Domain\Chauffage\{Chauffage, ChauffageRepository};
use App\Domain\Common\Type\Id;
use App\Domain\Eclairage\{Eclairage, EclairageRepository};
use App\Domain\Ecs\{Ecs, EcsRepository};
use App\Domain\Enveloppe\{Enveloppe, EnveloppeRepository};
use App\Domain\Production\{Production, ProductionRepository};
use App\Domain\Refroidissement\{Refroidissement, RefroidissementRepository};
use App\Domain\Ventilation\{Ventilation, VentilationRepository};
use App\Domain\Visite\{Visite, VisiteRepository};

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
        private VisiteRepository $visite_repository,
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
        $visite = $this->visite_repository->find($id);
        $eclairage = $this->eclairage_repository->find($id);

        return new Simulation(
            audit: $audit,
            enveloppe: $enveloppe,
            chauffage: $chauffage,
            ecs: $ecs,
            ventilation: $ventilation,
            refroidissement: $refroidissement,
            production: $production,
            visite: $visite,
            eclairage: $eclairage,
        );
    }

    public function build(
        Audit $audit,
        Enveloppe $enveloppe,
        Chauffage $chauffage,
        Ecs $ecs,
        Ventilation $ventilation,
        Refroidissement $refroidissement,
        Production $production,
        Visite $visite,
        Eclairage $eclairage,
    ): Simulation {
        return new Simulation(
            audit: $audit,
            enveloppe: $enveloppe,
            chauffage: $chauffage,
            ecs: $ecs,
            ventilation: $ventilation,
            refroidissement: $refroidissement,
            production: $production,
            visite: $visite,
            eclairage: $eclairage,
        );
    }
}
