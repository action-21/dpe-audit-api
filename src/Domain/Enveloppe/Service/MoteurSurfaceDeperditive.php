<?php

namespace App\Domain\Enveloppe\Service;

use App\Domain\Enveloppe\Enveloppe;
use App\Domain\Lnc\Service\MoteurSurfaceDeperditive as MoteurSurfaceDeperditiveLnc;
use App\Domain\Mur\Service\MoteurSurfaceDeperditive as MoteurSurfaceDeperditiveMur;
use App\Domain\PlancherBas\Service\MoteurSurfaceDeperditive as MoteurSurfaceDeperditivePlancherBas;
use App\Domain\PlancherHaut\Service\MoteurSurfaceDeperditive as MoteurSurfaceDeperditivePlancherHaut;

final class MoteurSurfaceDeperditive
{
    public function __construct(
        private MoteurSurfaceDeperditiveLnc $moteur_surface_deperditive_lnc,
        private MoteurSurfaceDeperditiveMur $moteur_surface_deperditive_mur,
        private MoteurSurfaceDeperditivePlancherBas $moteur_surface_deperditive_plancher_bas,
        private MoteurSurfaceDeperditivePlancherHaut $moteur_surface_deperditive_plancher_haut,
    ) {}

    public function __invoke(Enveloppe $entity): void
    {
        $entity->parois()->murs()->calcule_surface_deperditive($this->moteur_surface_deperditive_mur);
        $entity->parois()->planchers_bas()->calcule_surface_deperditive($this->moteur_surface_deperditive_plancher_bas);
        $entity->parois()->planchers_hauts()->calcule_surface_deperditive($this->moteur_surface_deperditive_plancher_haut);
        $entity->locaux_non_chauffes()->calcule_surface_deperditive($this->moteur_surface_deperditive_lnc);
    }
}
