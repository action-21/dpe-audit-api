<?php

namespace App\Domain\Enveloppe;

use App\Domain\Enveloppe\Service\{MoteurApport, MoteurInertie, MoteurPerformance, MoteurSurfaceDeperditive};
use App\Domain\Simulation\Simulation;

/**
 * @see App\Domain\Audit\AuditService::calcule()
 */
final class EnveloppeService
{
    public function __construct(
        private MoteurSurfaceDeperditive $moteur_surface_deperditive,
        private MoteurApport $moteur_apport,
        private MoteurInertie $moteur_inertie,
        private MoteurPerformance $moteur_performance,
    ) {}

    public function calcule_surface_deperditive(Enveloppe $entity): Enveloppe
    {
        $entity->calcule_surface_deperditive(moteur: $this->moteur_surface_deperditive);
        return $entity;
    }

    public function calcule_inertie(Enveloppe $entity): Enveloppe
    {
        $entity->calcule_inertie(moteur: $this->moteur_inertie);
        return $entity;
    }

    public function calcule_performance(Enveloppe $entity, Simulation $simulation): Enveloppe
    {
        $entity->calcule_performance(moteur: $this->moteur_performance, simulation: $simulation);
        return $entity;
    }

    public function calcule_apport(Enveloppe $entity, Simulation $simulation): Enveloppe
    {
        $entity->calcule_apport(moteur: $this->moteur_apport, simulation: $simulation);
        return $entity;
    }
}
